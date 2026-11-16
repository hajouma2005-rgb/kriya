
<?php
require 'inc.php';

// --------------------------------------------------
// 1. SESSION AND SECURITY
// --------------------------------------------------

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

// --------------------------------------------------
// 2. GET CAR INFORMATION
// --------------------------------------------------

$carId = (int)($_GET['id'] ?? 0);

$st = $pdo->prepare("
    SELECT
        c.*,
        u.name AS owner_name,
        u.role AS owner_role,
        u.phone
    FROM cars c
    JOIN users u ON u.id = c.owner_id
    WHERE c.id = ?
");

$st->execute([$carId]);

$c = $st->fetch(PDO::FETCH_ASSOC);

if (!$c) {
    http_response_code(404);
    die('Car not found');
}

// --------------------------------------------------
// 3. INITIALIZE VARIABLES
// --------------------------------------------------

$msg = '';
$u = me();

// --------------------------------------------------
// 4. HANDLE BOOKING REQUEST
// --------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Only clients can book
    need(['client']);

    // Verify CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($csrfToken, $submittedToken)) {

        $msg = 'Invalid request. Please refresh the page.';

    } else {

        $s = $_POST['start'] ?? '';
        $e2 = $_POST['end'] ?? '';

        // Validate date format
        $startDate = DateTimeImmutable::createFromFormat('!Y-m-d', $s);
        $endDate = DateTimeImmutable::createFromFormat('!Y-m-d', $e2);

        $validStart = $startDate && $startDate->format('Y-m-d') === $s;
        $validEnd = $endDate && $endDate->format('Y-m-d') === $e2;

        $today = new DateTimeImmutable('today');

        if (!$validStart || !$validEnd) {

            $msg = 'Please select valid dates.';

        } elseif ($startDate < $today) {

            $msg = 'The starting date cannot be in the past.';

        } elseif ($endDate < $startDate) {

            $msg = 'The ending date must be after or equal to the starting date.';

        } else {

            // Calculate rental days (including first day)
            $days = $startDate->diff($endDate)->days + 1;

            // Calculate total using database price
            $pricePerDay = (float)$c['price_per_day'];

            if ($pricePerDay <= 0) {

                $msg = 'This car currently has an invalid rental price.';

            } else {

                $total = $days * $pricePerDay;

                try {

                    // Start database transaction
                    $pdo->beginTransaction();

                    // Lock car row to prevent simultaneous
                    // booking requests for the same car
                    $lock = $pdo->prepare("
                        SELECT id
                        FROM cars
                        WHERE id = ?
                        FOR UPDATE
                    ");

                    $lock->execute([$c['id']]);

                    if (!$lock->fetch()) {

                        throw new Exception('Car no longer exists.');

                    }

                    // Check overlapping bookings
                    // Pending and accepted bookings block dates
                    $check = $pdo->prepare("
                        SELECT id
                        FROM bookings
                        WHERE car_id = ?
                        AND status IN ('pending', 'accepted')
                        AND start_date <= ?
                        AND end_date >= ?
                        LIMIT 1
                    ");

                    $check->execute([
                        $c['id'],
                        $e2,
                        $s
                    ]);

                    if ($check->fetch()) {

                        $pdo->rollBack();

                        $msg = 'Sorry! This car is already booked or awaiting confirmation for these dates. Please choose different dates.';

                    } else {

                        // Insert booking with pending status
                        $insert = $pdo->prepare("
                            INSERT INTO bookings (
                                car_id,
                                client_id,
                                start_date,
                                end_date,
                                total,
                                status
                            )
                            VALUES (?, ?, ?, ?, ?, 'pending')
                        ");

                        $insert->execute([
                            $c['id'],
                            $u['id'],
                            $s,
                            $e2,
                            $total
                        ]);

                        // Save booking
                        $pdo->commit();

                        // Prevent form resubmission
                        header('Location: dashboard.php?sent=1');
                        exit;
                    }

                } catch (Throwable $ex) {

                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }

                    error_log($ex->getMessage());

                    $msg = 'Something went wrong while sending your booking request. Please try again.';
                }
            }
        }
    }
}

// --------------------------------------------------
// 5. PAGE HEADER
// --------------------------------------------------

head($c['brand'] . ' ' . $c['model']);

// --------------------------------------------------
// 6. GET CAR IMAGES
// --------------------------------------------------

$im = $pdo->prepare("
    SELECT filename
    FROM car_images
    WHERE car_id = ?
");

$im->execute([$c['id']]);

$im = $im->fetchAll(PDO::FETCH_COLUMN);

// Use main image if no gallery images exist
if (!$im && !empty($c['image'])) {
    $im = [$c['image']];
}

?>

<!-- ================================================
     CAR DETAILS SECTION
================================================ -->

<section class="wrap detail">

    <!-- CAR GALLERY -->

    <div>

        <div
            class="pic big"
            id="main"
            style="<?= !empty($im)
                ? "background-image:url('uploads/" . e($im[0]) . "')"
                : '' ?>"
        >
            <?= empty($im) ? carsvg($c['brand']) : '' ?>
        </div>

        <!-- THUMBNAILS -->

        <?php if (count($im) > 1): ?>

            <div class="thumbs">

                <?php foreach ($im as $f): ?>

                    <button
                        type="button"
                        aria-label="Show photo"
                        style="background-image:url('uploads/<?= e($f) ?>')"
                        data-src="uploads/<?= e($f) ?>"
                    ></button>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>


    <!-- CAR INFORMATION -->

    <div>

        <h1 class="h1">

            <?= e($c['brand'] . ' ' . $c['model']) ?>

            <small><?= (int)$c['year'] ?></small>

        </h1>


        <!-- SPECIFICATIONS -->

        <ul class="specs">

            <li><?= e($c['city']) ?></li>

            <li><?= e($c['transmission']) ?></li>

            <li><?= e($c['fuel']) ?></li>

            <li><?= (int)$c['seats'] ?> seats</li>

        </ul>


        <!-- DESCRIPTION -->

        <p>
            <?= nl2br(e($c['description'])) ?>
        </p>


        <!-- OWNER DETAILS -->

        <p class="owner">

            Listed by

            <b><?= e($c['owner_name']) ?></b>

            (<?= $c['owner_role'] === 'agency'
                ? 'agency'
                : 'private owner' ?>)

            <?= $u ? ' · ' . e($c['phone']) : '' ?>

        </p>


        <!-- ==========================================
             BOOKING FORM
        =========================================== -->

        <form
            method="post"
            class="book"
            data-price="<?= (float)$c['price_per_day'] ?>"
        >

            <!-- PRICE -->

            <h3>

                <?= number_format((float)$c['price_per_day'], 0) ?>

                MAD

                <small>/ day</small>

            </h3>


            <!-- CSRF TOKEN -->

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken) ?>"
            >


            <!-- ERROR MESSAGE -->

            <?php if ($msg): ?>

                <p class="err">
                    <?= e($msg) ?>
                </p>

            <?php endif; ?>


            <!-- START DATE -->

            <label>

                From

                <input
                    type="date"
                    name="start"
                    required
                    min="<?= date('Y-m-d') ?>"
                    value="<?= e($_POST['start'] ?? '') ?>"
                >

            </label>


            <!-- END DATE -->

            <label>

                To

                <input
                    type="date"
                    name="end"
                    required
                    min="<?= date('Y-m-d') ?>"
                    value="<?= e($_POST['end'] ?? '') ?>"
                >

            </label>


            <!-- TOTAL PRICE -->

            <p class="total">
                Pick your dates to see the total.
            </p>


            <!-- BOOKING BUTTON -->

            <?php if (!$u): ?>

                <a class="btn" href="login.php">
                    Log in to book
                </a>

            <?php elseif ($u['role'] !== 'client'): ?>

                <p>
                    Only client accounts can book.
                </p>

            <?php else: ?>

                <button type="submit" class="btn">
                    Send booking request
                </button>

            <?php endif; ?>

        </form>

    </div>

</section>


<!-- ================================================
     JAVASCRIPT
================================================ -->

<script>

// ------------------------------------------
// IMAGE GALLERY
// ------------------------------------------

document.querySelectorAll('.thumbs button').forEach(button => {

    button.addEventListener('click', function () {

        const main = document.getElementById('main');

        main.style.backgroundImage =
            `url("${this.dataset.src}")`;

    });

});


// ------------------------------------------
// AUTOMATIC PRICE CALCULATION
// ------------------------------------------

const bookingForm = document.querySelector('.book');

if (bookingForm) {

    const price = Number(bookingForm.dataset.price);

    const startInput =
        bookingForm.querySelector('[name="start"]');

    const endInput =
        bookingForm.querySelector('[name="end"]');

    const totalDisplay =
        bookingForm.querySelector('.total');


    function calculateTotal() {

        const start = startInput.value;

        const end = endInput.value;

        // Ending date cannot be before starting date
        endInput.min = start || endInput.min;

        if (!start || !end) {

            totalDisplay.textContent =
                'Pick your dates to see the total.';

            return;
        }

        // Convert dates to UTC timestamps
        const startParts = start.split('-').map(Number);

        const endParts = end.split('-').map(Number);

        const startTime = Date.UTC(
            startParts[0],
            startParts[1] - 1,
            startParts[2]
        );

        const endTime = Date.UTC(
            endParts[0],
            endParts[1] - 1,
            endParts[2]
        );

        const days = Math.round(
            (endTime - startTime) / 86400000
        ) + 1;


        if (days < 1) {

            totalDisplay.textContent =
                'End date must be after or equal to start date.';

            return;
        }


        // Calculate total
        const total = days * price;

        totalDisplay.textContent =
            `${days} day(s) × ${price.toLocaleString()} MAD = ${total.toLocaleString()} MAD`;

    }


    // Update when dates change
    startInput.addEventListener('change', calculateTotal);

    endInput.addEventListener('change', calculateTotal);

    // Calculate if dates already exist
    calculateTotal();

}

</script>


<?php

// PAGE FOOTER
foot();

?>
