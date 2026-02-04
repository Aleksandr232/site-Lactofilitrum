<?php
require_once __DIR__ . '/php/config.php';

$timestamp = time();
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
    header('Location: /');
    exit;
}

$conn = connectDB();
$stmt = $conn->prepare("SELECT * FROM podcasts WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    header('Location: /');
    exit;
}

function asset_url($path) {
    if (!$path) return '';
    if (strpos($path, 'http') === 0) return $path;
    return '/' . ltrim($path, '/');
}

$img_url = asset_url($p['image']);
$author_photo_url = asset_url($p['author_photo']);
$video_url = asset_url($p['video_path']);
$extra_href = !empty($p['extra_link']) ? (strpos($p['extra_link'], 'http') === 0 ? $p['extra_link'] : '/' . ltrim($p['extra_link'], '/')) : '#';
$link_text = !empty($p['additional_link']) ? $p['additional_link'] : 'Подписаться';

// Абсолютные пути от корня — иначе на /single/slug стили и скрипты ломаются (404)
$base = '/';
?>
<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="icon" type="image/x-icon" href="<?php echo $base; ?>frontend/img/favicon/favicon.ico?v=<?php echo $timestamp; ?>" />
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base; ?>frontend/img/favicon/favicon-32x32.png?v=<?php echo $timestamp; ?>" />
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $base; ?>frontend/img/favicon/favicon-16x16.png?v=<?php echo $timestamp; ?>" />
		<link rel="apple-touch-icon" href="<?php echo $base; ?>frontend/img/favicon/apple-touch-icon.png?v=<?php echo $timestamp; ?>" />
		<meta name="description" content="<?php echo htmlspecialchars($p['description'] ?? $p['title'], ENT_QUOTES, 'UTF-8'); ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo $base; ?>frontend/css/swiper-bundle.min.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo $base; ?>frontend/css/choices.min.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo $base; ?>frontend/css/aos.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo $base; ?>frontend/css/fancybox.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo $base; ?>frontend/css/style.css?v=<?php echo $timestamp; ?>" />
		<style>
			.single_content {
				max-width: 100%;
				width: 100%;
				overflow-x: auto;
				overflow-y: auto;
				max-height: 600px;
				padding: 1rem;
				border-radius: 4px;
				word-wrap: break-word;
				overflow-wrap: break-word;
			}
			.single_content * {
				max-width: 100%;
			}
			.single_content img {
				max-width: 100%;
				height: auto;
			}
			.single_content table {
				max-width: 100%;
				display: block;
				overflow-x: auto;
			}
		</style>
		<title><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?> — Lactofilitrum</title>
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
<body>
	<div class="wrapper">
		<div class="mobile_header_wrapper">
			<div class="container">
				<div class="mobile_header_top d_flex a_items_center j_content_between">
					<div class="mobile_header_logo">
						<a href="/">
							<img src="<?php echo $base; ?>frontend/img/logo_header.svg" alt=""/>
						</a>
					</div>
					<div class="mobile_header_close">
						<img src="<?php echo $base; ?>frontend/img/icon_menu_close.svg" alt=""/>
					</div>
				</div>
				<div class="mobile_header_menu">
					<ul>
						<li><a href="/#podcasts">Подкасты</a></li>
						<li><a href="/">О Лактофильтруме</a></li>
						<li><a href="/#library-remission">Библиотека ремиссии</a></li>
						<li><a href="/">Получить бонус</a></li>
					</ul>
				</div>
			</div>
		</div>
		<header id="header" class="header">
			<div class="container">
				<div class="header_top d_flex j_content_between">
					<div class="header_top_left">
						<div class="header_logo">
							<a href="/">
								<img src="<?php echo $base; ?>frontend/img/logo_header.svg" alt=""/>
							</a>
						</div>
						<div class="header_tags d_flex a_items_center">
							<div class="header_tag">педиатрам</div>
							<div class="header_tag">терапевтам</div>
							<div class="header_tag">дерматологам</div>
						</div>
					</div>
					<div class="header_top_right">
						<div class="header_menu">
							<ul class="d_flex a_items_center">
								<li><a href="/#podcasts">Подкасты</a></li>
								<li><a href="/">О Лактофильтруме</a></li>
								<li><a href="/#library-remission">Библиотека ремиссии</a></li>
								<li><a href="/">Получить бонус</a></li>
							</ul>
						</div>
					</div>
					<div class="menu_btn">
						<img src="<?php echo $base; ?>frontend/img/icon_menu_btn.svg" alt=""/>
					</div>
				</div>
			</div>
		</header>
		<div class="main">
			<div class="container">
				<h1 class="single_title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
				<div class="single_speaker d_flex a_items_center">
					<div class="single_speaker_image">
						<?php if ($author_photo_url): ?>
							<img src="<?php echo htmlspecialchars($author_photo_url, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['author'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
						<?php else: ?>
							<img src="<?php echo $base; ?>frontend/img/temp/single_speaker_image.jpg" alt=""/>
						<?php endif; ?>
					</div>
					<div class="single_speaker_info">
						<div class="single_speaker_title">Спикер</div>
						<div class="single_speaker_name"><?php echo htmlspecialchars($p['author'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
					</div>
				</div>
				<div class="single_cols d_flex f_wrap">
					<div class="single_col single_col_left">
						<div class="single_content">
							<!-- <?php if (!empty($p['description'])): ?>
								<p><strong><?php echo nl2br(htmlspecialchars($p['description'], ENT_QUOTES, 'UTF-8')); ?></strong></p>
							<?php endif; ?> -->
							<!-- <p><strong>Профессор дает практические советы, как общаться с эмоциональными мамами детей-атопиков, как правильно объяснять схему лечения и обеспечить соблюдение рекомендаций</strong></p>
                            <ul>
                                <li>Эмпатия и доверие: как выстроить контакт с родителем ребёнка-атопика</li>
                                <li>Информирование и сопровождение: как обучать и мотивировать родителей</li>
                            </ul>
                            <p>Подпишись и получи чек-лист от гостя</p> -->
							<?php if (!empty($p['podcasts_text'])): ?>
								<?php echo $p['podcasts_text']; ?>
							<?php else: ?>
								<p>Текст не загружен...</p>
							<?php endif; ?>
						</div>
						<div class="single_btns d_flex">
							<?php if ($video_url): ?>
								<div class="single_btn">
									<a href="<?php echo htmlspecialchars($video_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn_blue" data-fancybox>Слушать</a>
								</div>
							<?php endif; ?>
							<?php if ($extra_href !== '#'): ?>
								<div class="single_btn">
									<a href="<?php echo htmlspecialchars($extra_href, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn_blue_transparent"<?php echo $extra_href[0] === 'h' ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo htmlspecialchars($link_text, ENT_QUOTES, 'UTF-8'); ?></a>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="single_col single_col_right">
						<div class="single_image">
							<?php if ($img_url): ?>
								<img src="<?php echo htmlspecialchars($img_url, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>"/>
							<?php else: ?>
								<img src="<?php echo $base; ?>frontend/img/temp/single_image.jpg" alt=""/>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<footer id="footer" class="footer">
			<div class="footer_top">
				<div class="footer_for_specialist">
					<img src="<?php echo $base; ?>frontend/img/svg/footer_specialist.svg" alt=""/>
				</div>
			</div>
			<div class="footer_bottom">
				<div class="container container--small">
					<div class="footer_logo">
						<img src="<?php echo $base; ?>frontend/img/svg/footer_logo.svg" alt=""/>
					</div>
					<div class="footer_bottom_cols d_flex j_content_between f_wrap">
						<div class="footer_bottom_left">
							<div class="footer_copyright">© 2023 АО «Отисифарм». Все права защищены</div>
							<div class="footer_privacy">При использовании материалов ссылка <br>на источник обязательна.</div>
						</div>
						<div class="footer_bottom_right d_flex">
							<div class="footer_menu footer_menu_1">
								<ul>
									<li><a href="#">Синапс</a></li>
									<li><a href="#">Важно о мозге</a></li>
								</ul>
							</div>
							<div class="footer_menu footer_menu_2">
								<ul>
									<li><a href="#">О компании</a></li>
									<li><a href="#">Препараты</a></li>
									<li><a href="#">Безопасность препаратов</a></li>
									<li><a href="#">Контакты</a></li>
								</ul>
							</div>
							<div class="footer_infos">
								<div class="footer_info">АО «Отисифарм»</div>
								<div class="footer_info">123112, г. Москва, вн.тер.г. <br>муниципальный округ Пресненский, <br>ул. Тестовская, д. 10, помещ. 1/16</div>
								<div class="footer_info">
									Телефон: <a href="tel:+74952211800">+7 (495) 221-18-00</a><br>
									Факс: <a href="tel:+74952211802">+7 (495) 221-18-02</a><br>
									E-mail: <a href="mailto:synapse@otcpharm.ru">synapse@otcpharm.ru</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/jquery.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/swiper-bundle.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/choices.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/gsap.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/ScrollTrigger.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/aos.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/fancybox.umd.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="<?php echo $base; ?>frontend/js/main.js?v=<?php echo $timestamp; ?>"></script>
</body>
</html>
