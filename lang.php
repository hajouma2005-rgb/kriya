<?php
// ===== Languages: add a new one by adding it to $LANGS and a 3rd value in every line of $D =====
$LANGS=['en'=>'EN','fr'=>'FR','ar'=>'عربي'];
if(isset($_GET['lang'])&&isset($LANGS[$_GET['lang']])){ $_SESSION['lang']=$_GET['lang']; setcookie('lang',$_GET['lang'],time()+31536000,'/'); }
$L=$_SESSION['lang']??($_COOKIE['lang']??'en'); if(!isset($LANGS[$L])) $L='en';

// English text => [French, Arabic].  A key starting with "~" is replaced inside longer text.
$D=[
'Browse cars'=>['Parcourir les voitures','تصفح السيارات'],
'For owners &amp; agencies'=>["Propriétaires &amp; agences",'للملاك والوكالات'],
'My trips'=>['Mes locations','رحلاتي'],'My fleet'=>['Mon parc','سياراتي'],
'~Log out ('=>['Déconnexion (','تسجيل الخروج ('],
'Log in'=>['Connexion','تسجيل الدخول'],'Sign up'=>['Inscription','إنشاء حساب'],
'car rental between agencies, owners and drivers.'=>['location de voitures entre agences, propriétaires et conducteurs.','تأجير السيارات بين الوكالات والملاك والسائقين.'],
'Agency'=>['Agence','وكالة'],'Private owner'=>['Particulier','مالك خاص'],'/day'=>['/jour','/يوم'],'/ day'=>['/ jour','/ يوم'],
'~ seats'=>[' places',' مقاعد'],'~ MAD/day'=>[' MAD/jour',' درهم/يوم'],'~ MAD/month'=>[' MAD/mois',' درهم/شهر'],'~ MAD'=>[' MAD',' درهم'],
'Petrol'=>['Essence','بنزين'],'Diesel'=>['Diesel','ديزل'],'Hybrid'=>['Hybride','هجين'],'Electric'=>['Électrique','كهربائي'],
'Manual'=>['Manuelle','يدوي'],'Automatic'=>['Automatique','أوتوماتيك'],
'Rent a car'=>['Louer une voiture','استأجر سيارة'],
'Rent a car from people who love theirs.'=>["Louez une voiture auprès de ceux qui l'adorent.",'استأجر سيارة من أشخاص يحبونها.'],
'Trusted agencies and private owners, one search. Pick a city, compare prices and book in minutes.'=>["Agences de confiance et particuliers, une seule recherche. Choisissez une ville, comparez les prix et réservez en quelques minutes.",'وكالات موثوقة وملاك خاصون في بحث واحد. اختر مدينة وقارن الأسعار واحجز في دقائق.'],
'CITY'=>['VILLE','المدينة'],'City'=>['Ville','المدينة'],'Search cars'=>['Chercher une voiture','ابحث عن سيارة'],
'cars listed'=>['voitures en ligne','سيارة معروضة'],'agencies &amp; owners'=>['agences et propriétaires','وكالات وملاك'],'to list your car'=>['pour publier votre voiture','لعرض سيارتك'],
'Fresh on the road'=>['Nouveautés sur la route','جديد على الطريق'],'See all cars'=>['Voir toutes les voitures','عرض كل السيارات'],
'No cars yet. Be the first to list one!'=>["Aucune voiture pour l'instant. Soyez le premier à en publier une !",'لا توجد سيارات بعد. كن أول من يعرض سيارة!'],
'How it works'=>['Comment ça marche','كيف يعمل الموقع'],
'Search'=>['Rechercher','ابحث'],'Request'=>['Demander','اطلب'],'Drive'=>['Conduire','سُق'],
'Choose a city and filter by price, fuel and owner type.'=>['Choisissez une ville et filtrez par prix, carburant et type de propriétaire.','اختر مدينة وصفِّ حسب السعر والوقود ونوع المالك.'],
'Pick your dates and see the exact total before you send.'=>['Choisissez vos dates et voyez le total exact avant d\'envoyer.','اختر التواريخ وشاهد المبلغ الإجمالي قبل الإرسال.'],
'The owner accepts, you meet, and you hit the road.'=>["Le propriétaire accepte, vous vous retrouvez et c'est parti.",'يوافق المالك، تلتقيان، وتنطلق في الطريق.'],
'Agencies you can trust'=>['Des agences de confiance','وكالات يمكنك الوثوق بها'],
'~ cars found'=>[' voitures trouvées',' سيارات موجودة'],'~ car found'=>[' voiture trouvée',' سيارة موجودة'],'~ cars'=>[' voitures',' سيارات'],
'Your car sleeps 25 days a month.'=>['Votre voiture dort 25 jours par mois.','سيارتك متوقفة 25 يوماً في الشهر.'],
'Agencies and private owners can subscribe and list cars with photos. Every plan is free during launch.'=>["Agences et particuliers peuvent s'abonner et publier leurs voitures avec photos. Tous les forfaits sont gratuits pendant le lancement.",'يمكن للوكالات والملاك الاشتراك وعرض سياراتهم مع الصور. جميع الباقات مجانية خلال الإطلاق.'],
'See plans'=>['Voir les forfaits','عرض الباقات'],
'Compare prices, photos and owners, then send a request in one click.'=>['Comparez prix, photos et propriétaires, puis envoyez une demande en un clic.','قارن الأسعار والصور والملاك، ثم أرسل طلباً بنقرة واحدة.'],
'Brand or model'=>['Marque ou modèle','الماركة أو الطراز'],'Any fuel'=>["Tous carburants",'أي وقود'],
'Agencies &amp; owners'=>['Agences et particuliers','الوكالات والملاك'],'Agencies only'=>['Agences uniquement','الوكالات فقط'],'Private owners only'=>['Particuliers uniquement','الملاك الخاصون فقط'],
'Max'=>['Max','الحد الأقصى'],'Apply'=>['Appliquer','تطبيق'],
'No car matches. Try another city or raise the price limit.'=>['Aucune voiture ne correspond. Essayez une autre ville ou augmentez le prix maximum.','لا توجد سيارة مطابقة. جرّب مدينة أخرى أو ارفع حد السعر.'],
'~Listed by '=>['Publiée par ','معروضة من قبل '],'~(agency)'=>['(agence)','(وكالة)'],'~(private owner)'=>['(particulier)','(مالك خاص)'],
'From'=>['Du','من'],'To'=>['Au','إلى'],'Pick your dates to see the total.'=>['Choisissez vos dates pour voir le total.','اختر التواريخ لعرض الإجمالي.'],
'Log in to book'=>['Connectez-vous pour réserver','سجّل الدخول للحجز'],'Only client accounts can book.'=>['Seuls les comptes clients peuvent réserver.','الحجز متاح لحسابات العملاء فقط.'],
'Send booking request'=>['Envoyer la demande de réservation','إرسال طلب الحجز'],
'Pick valid dates (today or later, end after start).'=>['Choisissez des dates valides (à partir d\'aujourd\'hui, fin après le début).','اختر تواريخ صحيحة (من اليوم فصاعداً، والنهاية بعد البداية).'],
'End date must be after start date.'=>['La date de fin doit suivre la date de début.','يجب أن يكون تاريخ النهاية بعد البداية.'],'day'=>['jour','يوم'],'days'=>['jours','أيام'],
'Create your account'=>['Créez votre compte','أنشئ حسابك'],'I want to rent'=>['Je veux louer','أريد الاستئجار'],'I own a car'=>["J'ai une voiture",'أملك سيارة'],'I run an agency'=>['Je gère une agence','أدير وكالة'],
'Full name or agency name'=>["Nom complet ou nom de l'agence",'الاسم الكامل أو اسم الوكالة'],'Email'=>['E-mail','البريد الإلكتروني'],'Password'=>['Mot de passe','كلمة المرور'],
'Password (6+ characters)'=>['Mot de passe (6 caractères min.)','كلمة المرور (6 أحرف على الأقل)'],'Phone'=>['Téléphone','الهاتف'],
'Already a member?'=>['Déjà membre ?','لديك حساب؟'],'New here?'=>['Nouveau ici ?','جديد هنا؟'],'Create an account'=>['Créer un compte','أنشئ حساباً'],'Welcome back'=>['Bon retour','مرحباً بعودتك'],
'Wrong email or password.'=>['E-mail ou mot de passe incorrect.','البريد أو كلمة المرور غير صحيحة.'],
'Password needs at least 6 characters.'=>['Le mot de passe doit contenir au moins 6 caractères.','يجب أن تتكون كلمة المرور من 6 أحرف على الأقل.'],
'This email is already registered.'=>['Cet e-mail est déjà utilisé.','هذا البريد مسجّل مسبقاً.'],
'Dashboard'=>['Tableau de bord','لوحة التحكم'],'~Hello, '=>['Bonjour, ','مرحباً، '],
'Request sent. The owner will answer soon.'=>['Demande envoyée. Le propriétaire répondra bientôt.','تم إرسال الطلب. سيرد المالك قريباً.'],
'No trips yet.'=>['Aucune location pour le moment.','لا توجد رحلات بعد.'],'Find your first car'=>['Trouvez votre première voiture','ابحث عن سيارتك الأولى'],
'Booking requests'=>['Demandes de réservation','طلبات الحجز'],'No requests yet.'=>['Aucune demande pour le moment.','لا توجد طلبات بعد.'],
'wants'=>['souhaite','يريد'],'Accept'=>['Accepter','قبول'],'Refuse'=>['Refuser','رفض'],'pending'=>['en attente','قيد الانتظار'],'accepted'=>['acceptée','مقبول'],'refused'=>['refusée','مرفوض'],
'List a car'=>['Publier une voiture','اعرض سيارة'],'Brand'=>['Marque','الماركة'],'Model'=>['Modèle','الطراز'],'Year'=>['Année','السنة'],
'Price per day (MAD)'=>['Prix par jour (MAD)','السعر لليوم (درهم)'],'Seats'=>['Places','المقاعد'],
'Tell drivers what makes this car great'=>['Dites aux conducteurs ce qui rend cette voiture géniale','أخبر السائقين ما يميز هذه السيارة'],
'Photos (up to 6, first one is the cover)'=>['Photos (6 max, la première est la couverture)','الصور (حتى 6، الأولى هي الغلاف)'],
'Publish car'=>['Publier la voiture','انشر السيارة'],'Delete'=>['Supprimer','حذف'],'Delete this car?'=>['Supprimer cette voiture ?','حذف هذه السيارة؟'],
'Activate a plan first to list cars.'=>["Activez d'abord un forfait pour publier des voitures.",'فعّل باقة أولاً لعرض السيارات.'],
'Your plan limit is reached. Upgrade to add more cars.'=>['Limite du forfait atteinte. Passez à un forfait supérieur.','وصلت إلى حد باقتك. رقِّ باقتك لإضافة المزيد.'],
'Photos must be JPG, PNG or WEBP, under 3 MB each.'=>['Photos JPG, PNG ou WEBP, 3 Mo maximum chacune.','الصور بصيغة JPG أو PNG أو WEBP وبحجم أقل من 3 ميغابايت.'],
'Plan'=>['Forfait','الباقة'],'~ · up to '=>[' · jusqu\'à ',' · حتى '],'~ cars · '=>[' voitures · ',' سيارات · '],'~unlimited'=>['illimité','غير محدود'],
'Change plan'=>['Changer de forfait','تغيير الباقة'],'You have no plan yet.'=>["Vous n'avez pas encore de forfait.",'ليس لديك باقة بعد.'],'Pick a free plan'=>['Choisir un forfait gratuit','اختر باقة مجانية'],
'Plans'=>['Forfaits','الباقات'],'List your cars. Pay nothing for now.'=>['Publiez vos voitures. Ne payez rien pour le moment.','اعرض سياراتك. لا تدفع شيئاً حالياً.'],
'Every plan is free during our launch. Prices apply later, and we will tell you before.'=>['Tous les forfaits sont gratuits pendant le lancement. Les tarifs s\'appliqueront plus tard, et nous vous préviendrons.','جميع الباقات مجانية خلال الإطلاق. ستُطبَّق الأسعار لاحقاً وسنخبرك قبل ذلك.'],
'Most popular'=>['Le plus populaire','الأكثر شعبية'],'Starter'=>['Débutant','المبتدئ'],'Pro'=>['Pro','المحترف'],
'free during launch'=>['gratuit pendant le lancement','مجاني خلال الإطلاق'],
'For private owners'=>['Pour les particuliers','للملاك الخاصين'],'For small agencies'=>['Pour les petites agences','للوكالات الصغيرة'],'For large agencies'=>['Pour les grandes agences','للوكالات الكبيرة'],
'Up to 3 cars'=>["Jusqu'à 3 voitures",'حتى 3 سيارات'],'Up to 15 cars'=>["Jusqu'à 15 voitures",'حتى 15 سيارة'],'Unlimited cars'=>['Voitures illimitées','سيارات غير محدودة'],
'Photo gallery (6 photos per car)'=>['Galerie photos (6 par voiture)','معرض صور (6 صور لكل سيارة)'],
'Booking requests by email and dashboard'=>['Demandes de réservation par e-mail et tableau de bord','طلبات الحجز عبر البريد ولوحة التحكم'],
'Agency badge on every listing'=>["Badge agence sur chaque annonce",'شارة الوكالة على كل إعلان'],'Priority in search results'=>['Priorité dans les résultats','أولوية في نتائج البحث'],'Dedicated support'=>['Assistance dédiée','دعم مخصص'],
'Sign up to start'=>['Inscrivez-vous pour commencer','سجّل للبدء'],'Create an owner or agency account to subscribe.'=>['Créez un compte propriétaire ou agence pour vous abonner.','أنشئ حساب مالك أو وكالة للاشتراك.'],
'Your current plan'=>['Votre forfait actuel','باقتك الحالية'],'Start free'=>['Commencer gratuitement','ابدأ مجاناً'],
];
function t($k,...$a){ global $L,$D; $s=$k; if($L!=='en'&&isset($D[$k])) $s=$D[$k][$L==='fr'?0:1]; return $a?vsprintf($s,$a):$s; }
// Translates the finished page: text between tags + placeholders. Attributes like value="" are never touched.
function i18n($h){
  global $L,$D; if($L==='en') return $h; $i=$L==='fr'?0:1; $ex=[]; $fr=[];
  foreach($D as $k=>$v){ if($k[0]==='~') $fr[substr($k,1)]=$v[$i]; else { $ex[$k]=$v[$i]; if(strlen($k)>=10) $fr[$k]=$v[$i]; } }
  $tr=function($s) use($ex,$fr){ $t=trim($s); if($t==='') return $s; if(isset($ex[$t])) return str_replace($t,$ex[$t],$s); return strtr($s,$fr); };
  $h=preg_replace_callback('/>([^<>]+)</',fn($m)=>'>'.$tr($m[1]).'<',$h);
  return preg_replace_callback('/(placeholder|aria-label)="([^"]*)"/',fn($m)=>$m[1].'="'.($ex[$m[2]]??$m[2]).'"',$h);
}
