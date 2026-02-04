<?php
$timestamp = time();
require_once 'php/config.php';
initializeDatabase();
$remissionItems = [];
try {
	$conn = connectDB();
	$stmt = $conn->prepare("SELECT id, title, description, image, pdf_path FROM remission_library ORDER BY created_at DESC");
	$stmt->execute();
	$remissionItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	error_log("Ошибка загрузки remission: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link type="text/css" media="all" rel="stylesheet" href="frontend/css/swiper-bundle.min.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="frontend/css/choices.min.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="frontend/css/aos.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="frontend/css/fancybox.css?v=<?php echo $timestamp; ?>" />
		<link type="text/css" media="all" rel="stylesheet" href="frontend/css/style.css?v=<?php echo $timestamp; ?>" />
		<title>Main</title>
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
						<a href="#">
							<img src="frontend/img/logo_header.svg" alt=""/>
						</a>
					</div>
					<div class="mobile_header_close">
						<img src="frontend/img/icon_menu_close.svg" alt=""/>
					</div>
				</div>
				<div class="mobile_header_menu">
					<ul>
						<li><a href="#podcasts">Подкасты</a></li>
						<li><a href="#">О Лактофильтруме</a></li>
						<li><a href="#library-remission">Библиотека ремиссии</a></li>
						<li><a href="#">Получить бонус</a></li>
					</ul>
				</div>
			</div>
		</div>
		<header id="header" class="header">
			<div class="container">
				<div class="header_top d_flex j_content_between">
					<div class="header_top_left">
						<div class="header_logo">
							<a href="#">
								<img src="frontend/img/logo_header.svg" alt=""/>
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
								<li><a href="#podcasts">Подкасты</a></li>
								<li><a href="#">О Лактофильтруме</a></li>
								<li><a href="#library-remission">Библиотека ремиссии</a></li>
								<li><a href="#">Получить бонус</a></li>
							</ul>
						</div>
					</div>
					<div class="menu_btn">
						<img src="frontend/img/icon_menu_btn.svg" alt=""/>
					</div>
				</div>
			</div>
		</header>
		<div class="first_bk">
			<div class="container">
				<div class="ftbk_inside_wrapper">
					<div class="ftbk_inside">
						<div class="ftbk_title" data-aos="fade-up">«Нетоксичный контент»</div>
						<div class="ftbk_desc" data-aos="fade-up">
							<p>Только самое полезное для практикующего врача <strong>без «шлаков и токсинов»</strong> о лечении атопического дерматита.</p>
							<p>Проект «очищает контент от шлаков и токсинов, оставляя только полезное, улучшая работу критического мышления».</p>
						</div>
						<div class="ftbk_btn" data-aos="fade-up">
							<a href="#" class="btn btn_green btn_small">Подписаться</a>
						</div>
					</div>
					<div class="ftbk_box" data-aos="fade-up">
						<div class="ftbk_box_inside">
							<div class="ftbk_box_title">Подробнее <br>о Лактофильтруме</div>
							<div class="ftbk_box_btn">
								<a href="#" class="btn btn_green btn_small">Смотреть</a>
							</div>
							<div class="ftbk_box_image">
								<img src="frontend/img/temp/ftbk_box_image.png" alt=""/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mission">
			<div class="container">
				<div class="mission_cols">
					<div class="mission_col mission_col_left">
						<div class="mission_content">
							<p>Современный врач тонет в потоке данных: новые исследования, клинические рекомендации, статьи, реклама препаратов, мнения экспертов и «народные» мифы.</p>
							<p><span>Каждый день приходится абсорбировать тонны информации,</span></p>
							<p>но где гарантия, что важное не ускользнёт, а ложное не повлияет на решение? Как в этом шуме найти сигнал? Нужны не только критическое мышление, но и время, которого у врача и так нет.</p>
						</div>
					</div>
					<div class="mission_circle">
						<div class="mission_circle_bg">
							<img src="frontend/img/bg_mission_circle.svg" alt=""/>
						</div>
						<div class="mission_circle_inside d_flex a_items_center j_content_center">Миссия</div>
						<div class="mission_circle_images">
							<img src="frontend/img/temp/mission_circle_image_1.png" class="mission_circle_image mission_circle_image_1" alt=""/>
							<img src="frontend/img/temp/mission_circle_image_2.png" class="mission_circle_image mission_circle_image_2" alt=""/>
						</div>
					</div>
					<div class="mission_col mission_col_right">
						<div class="mission_content">
							<p>В проекте «нетоксичный контент» вместе с экспертами была собрана ключевая информация о лечении атопического дерматита (АтД) и юридическая </p>
							<p><span>информация, которая необходима практикующему врачу в коротких форматах аудиоподкаста и инфографики.</span></p>
						</div>
					</div>
				</div>
				<div class="mission_btn">
					<a href="#" class="btn btn_green btn_small">Смотреть</a>
				</div>
			</div>
		</div>
		<div id="podcasts" class="podcasts slider_wrapper">
			<div class="container">
				<div class="title_head d_flex a_items_end j_content_between" data-aos="fade-up">
					<div class="title_head_left">
						<div class="title_bk">Подкасты с экспертами</div>
					</div>
					<div class="title_head_right">
						<div class="slider_nav d_flex a_items_center">
							<div class="slider_arr slider_arr_left">
								<svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="22.5" cy="22.5" r="22.5" fill="#E6F3F8"/>
									<path d="M26 15L16.9761 22.2191C16.4757 22.6195 16.4757 23.3805 16.9761 23.7809L26 31" stroke="#1878BC" stroke-width="2" stroke-linecap="round"/>
								</svg>									
							</div>
							<div class="slider_arr slider_arr_right">
								<svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="22.5" cy="22.5" r="22.5" transform="rotate(-180 22.5 22.5)" fill="#E6F3F8"/>
									<path d="M19 30L28.0239 22.7809C28.5243 22.3805 28.5243 21.6195 28.0239 21.2191L19 14" stroke="#1878BC" stroke-width="2" stroke-linecap="round"/>
								</svg>									
							</div>
						</div>
					</div>
				</div>
				<div class="podcasts_slider swiper" data-aos="fade-up" data-podcasts-dynamic>
					<div class="swiper-wrapper">
						<!-- Слайды подкастов подгружаются из API -->
						<div class="podcasts-slider-placeholder" style="padding: 2rem; text-align: center; color: #666;">
							Загрузка подкастов…
						</div>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>
		<div class="healing">
			<div class="healing_bg">
				<img src="frontend/img/bg_healing.png" alt=""/>
			</div>
			<div class="container">
				<div class="healing_basic">
					<div class="healing_top" data-aos="fade-up">
						<div class="healing_top_title">Лактофильтрум — лекарственный препарат, содержащий <br>комбинацию сорбента и пребиотика, </div>
						<div class="healing_top_desc">с показанный в дерматологии: в комплексной терапии аллергических заболеваний кожи <br>(атопический дерматит, крапивница)1,2</div>
					</div>
					<div class="healing_bottom d_flex">
						<div class="healing_image" data-aos="fade-up">
							<img src="frontend/img/temp/healing_product.png" alt=""/>
						</div>
						<div class="healing_boxes d_flex a_items_center">
							<div class="healing_box healing_box--left" data-aos="fade-up">
								<div class="healing_box_inside">
									<div class="healing_box_title">Воздействует на звенья оси «кишечник-кожа» благодаря комплексному составу, </div>
									<div class="healing_box_desc">что способствует формированию защитного фактора — нормальной микрофлоры кишечника<sup>3-6</sup></div>
								</div>
							</div>
							<div class="healing_box_sep" data-aos="fade-up"></div>
							<div class="healing_box healing_box--right" data-aos="fade-up">
								<div class="healing_box_inside">
									<div class="healing_box_title">Кишечник и кожа взаимосвязаны</div>
									<div class="healing_box_desc"> в среднем y 77-100% больных АтД и крапивницей* — дисбактериоз кишечника<sup>7</sup>. Механизм связи между кишечником и кожей называется «ось кишечник-кожа». Нарушение состава микробиота и сенсибилизация — частые спутники и триггеры аллергодерматозов<sup>8</sup>.</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="healing_info">
					<div class="healing_info_image_wrapper" data-aos="fade-up">
						<div class="healing_info_image">
							<img src="frontend/img/bg_healing_human.png" alt=""/>
						</div>
						<div class="healing_info_tooltips">
							<div class="healing_tooltips_item healing_tooltips_item--right" style="right: -34px; top: 226px;">
								<div class="healing_tooltip">Воспаления</div>
								<div class="healing_tooltip_overlay">Дисбактериоз, повышенная кишечная проницаемость</div>
							</div>
							<div class="healing_tooltips_item" style="left: -70px; top: 299px;">
								<div class="healing_tooltip">Нарушение регуляции <br>имунного ответа</div>
								<div class="healing_tooltip_overlay">Развитие аллергических заболеваний, поддержание воспаления в коже</div>
							</div>
							<div class="healing_tooltips_item" style="left: -40px; bottom: 307px;">
								<div class="healing_tooltip">Аллергия</div>
								<div class="healing_tooltip_overlay">Развитие аллергических заболеваний, поддержание воспаления в коже</div>
							</div>
						</div>
					</div>
					<div class="healing_info_items d_flex a_items_center" data-aos="fade-up">
						<div class="healing_info_item healing_info_item--left">
							<div class="healing_info_item_inside">
								<div class="healing_info_item_title">Связь между кишечником и кожей при АтД хорошо изучена и заключается в том, что</div>
								<div class="healing_info_item_desc">состояние микробиома кишечника может оказывать значительное влияние на течение и выраженность симптомов АтД</div>
							</div>
						</div>
						<div class="healing_info_item_sep"></div>
						<div class="healing_info_item healing_info_item--right">
							<div class="healing_info_item_inside">
								<div class="healing_info_item_title">Дисбаланс микрофлоры кишечника, <br>или по-другому </div>
								<div class="healing_info_item_desc">«дисбактериоз», может приводить к усилению воспалительных процессов <br>в организме</div>
							</div>
						</div>
					</div>
					<div class="healing_info_box" data-aos="fade-up">Пока не устранена причина частых и длительных обострений изнутри, рецидивы могут возникать вновь и вновь</div>
				</div>
			</div>
		</div>
		<div class="about bg_linear">
			<div class="about_bg_wrapper">
				<div class="about_bg_images">
					<img src="frontend/img/bg_about_1.png" alt="" class="about_bg_image about_bg_image-1">
				</div>
				<div class="about_bg_circle">
					<img src="frontend/img/bg_about_2_1.png" alt="" class="about_image about_image-2_1">
					<img src="frontend/img/bg_about_2_2.png" alt="" class="about_image about_image-2_2">
					<img src="frontend/img/bg_about_product.png" alt="" class="about_image about_image_product"/>
				</div>
			</div>
			<div class="about_content">
				<div class="container">
					<div class="title_bk title_bk--center">Лактофильтрум — препарат 2 в 1</div>
					<div class="about_items">
						<div class="row row_about_items d_flex f_wrap">
							<div class="col col-3">
								<div class="about_item">
									<div class="about_item_icon">
										<img src="frontend/img/temp/about_item_1.png" alt=""/>
									</div>
									<div class="about_item_title"><strong>Выводит «лишнее»:</strong>  очищает организм от токсинов, аллергенов, вредных бактерий за счёт лигнина</div>
								</div>
							</div>
							<div class="col col-3">
								<div class="about_item">
									<div class="about_item_icon">
										<img src="frontend/img/temp/about_item_2.png" alt=""/>
									</div>
									<div class="about_item_title"><strong>Способствует нормализации работы кишечника</strong></div>
								</div>
							</div>
							<div class="col col-3">
								<div class="about_item">
									<div class="about_item_icon">
										<img src="frontend/img/temp/about_item_3.png" alt=""/>
									</div>
									<div class="about_item_title">Имеет <strong>благоприятный профиль безопасности</strong></div>
								</div>
							</div>
						</div>
					</div>
					<div class="about_cols">
						<div class="about_col about_col_left">
							<div class="about_col_title">Пребиотик Лактулоза</div>
							<div class="about_col_box">
								<div class="about_col_items">
									<div class="about_col_item">Стимулирует развитие собственной защитной микрофлоры кишечника, подавляет рост патогенных микроорганизмов</div>
									<div class="about_col_item">Разрешена к применению у беременных*</div>
								</div>
							</div>
						</div>
						<div class="about_col about_col_right">
							<div class="about_col_title">Сорбет Лигнин</div>
							<div class="about_col_box">
								<div class="about_col_items">
									<div class="about_col_item">Обладает высокой сорбционной ёмкостью</div>
									<div class="about_col_item">Благоприятный профиль безопасности: разрешен к применению у беременных кормящим</div>
									<div class="about_col_item">Не травмирует слизистые оболочки желудка,кишечника</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="scheme bg_linear">
			<div class="container">
				<div class="title_bk title_bk--center" data-aos="fade-up">Схема применения</div>
				<div class="scheme_items_outer">
					<div class="scheme_items" data-aos="fade-up">
						<div class="row row_scheme_items d_flex f_wrap">
							<div class="col col-4">
								<div class="scheme_item">
									<div class="scheme_item_basic">
										<div class="scheme_basic_image">
											<img src="frontend/img/temp/scheme_item_1.png" alt=""/>
										</div>
										<div class="scheme_basic_title">1-3 года</div>
									</div>
									<div class="scheme_item_overlay">
										<div class="scheme_overlay_image">
											<img src="frontend/img/temp/scheme_item_1.png" alt=""/>
										</div>
										<div class="scheme_overlay_inside">
											<div class="scheme_overlay_top">
												<div class="scheme_overlay_title">1-3 года</div>
											</div>
											<div class="scheme_overlay_bottom">
												<div class="scheme_overlay_items">
													<div class="row row_scheme_overlay_items d_flex f_wrap">
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_1.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_2.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">за час до еды <br>и приема лекарств</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_3.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">раза в сутки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_4.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки принимают <br>внутрь, запивая водой</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col col-4">
								<div class="scheme_item">
									<div class="scheme_item_basic">
										<div class="scheme_basic_image">
											<img src="frontend/img/temp/scheme_item_2.png" alt=""/>
										</div>
										<div class="scheme_basic_title">3-7 лет</div>
									</div>
									<div class="scheme_item_overlay">
										<div class="scheme_overlay_image">
											<img src="frontend/img/temp/scheme_item_2.png" alt=""/>
										</div>
										<div class="scheme_overlay_inside">
											<div class="scheme_overlay_top">
												<div class="scheme_overlay_title">3-7 лет</div>
											</div>
											<div class="scheme_overlay_bottom">
												<div class="scheme_overlay_items">
													<div class="row row_scheme_overlay_items d_flex f_wrap">
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_1.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_2.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">за час до еды <br>и приема лекарств</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_3.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">раза в сутки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_4.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки принимают <br>внутрь, запивая водой</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col col-4">
								<div class="scheme_item">
									<div class="scheme_item_basic">
										<div class="scheme_basic_image">
											<img src="frontend/img/temp/scheme_item_3.png" alt=""/>
										</div>
										<div class="scheme_basic_title">8-12 лет</div>
									</div>
									<div class="scheme_item_overlay">
										<div class="scheme_overlay_image">
											<img src="frontend/img/temp/scheme_item_3.png" alt=""/>
										</div>
										<div class="scheme_overlay_inside">
											<div class="scheme_overlay_top">
												<div class="scheme_overlay_title">8-12 лет</div>
											</div>
											<div class="scheme_overlay_bottom">
												<div class="scheme_overlay_items">
													<div class="row row_scheme_overlay_items d_flex f_wrap">
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_1.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_2.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">за час до еды <br>и приема лекарств</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_3.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">раза в сутки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_4.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки принимают <br>внутрь, запивая водой</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col col-4">
								<div class="scheme_item">
									<div class="scheme_item_basic">
										<div class="scheme_basic_image">
											<img src="frontend/img/temp/scheme_item_4.png" alt=""/>
										</div>
										<div class="scheme_basic_title">старше 12 лет</div>
									</div>
									<div class="scheme_item_overlay">
										<div class="scheme_overlay_image">
											<img src="frontend/img/temp/scheme_item_4.png" alt=""/>
										</div>
										<div class="scheme_overlay_inside">
											<div class="scheme_overlay_top">
												<div class="scheme_overlay_title">старше 12 лет</div>
											</div>
											<div class="scheme_overlay_bottom">
												<div class="scheme_overlay_items">
													<div class="row row_scheme_overlay_items d_flex f_wrap">
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_1.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">тест таблетки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_2.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">тест за час до еды <br>и приема лекарств</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_3.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">тест раза в сутки</div>
															</div>
														</div>
														<div class="col col-2">
															<div class="scheme_overlay_item">
																<div class="scheme_overlay_item__icon">
																	<img src="frontend/img/temp/scheme_icon_4.png" alt=""/>
																</div>
																<div class="scheme_overlay_item__title">таблетки принимают <br>внутрь, запивая водой</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="library-remission" class="library slider_wrapper bg_linear">
			<div class="container">
				<div class="title_head d_flex a_items_end j_content_between" data-aos="fade-up">
					<div class="title_head_left">
						<div class="title_bk">Библиотека ремиссии</div>
						<div class="subtitle_bk">Материалы, которые помогут выйти на новый терапевтический уровень</div>
					</div>
					<div class="title_head_right">
						<div class="slider_nav d_flex a_items_center">
							<div class="slider_arr slider_arr_left">
								<svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="22.5" cy="22.5" r="22.5" fill="#E6F3F8"/>
									<path d="M26 15L16.9761 22.2191C16.4757 22.6195 16.4757 23.3805 16.9761 23.7809L26 31" stroke="#1878BC" stroke-width="2" stroke-linecap="round"/>
								</svg>									
							</div>
							<div class="slider_arr slider_arr_right">
								<svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="22.5" cy="22.5" r="22.5" transform="rotate(-180 22.5 22.5)" fill="#E6F3F8"/>
									<path d="M19 30L28.0239 22.7809C28.5243 22.3805 28.5243 21.6195 28.0239 21.2191L19 14" stroke="#1878BC" stroke-width="2" stroke-linecap="round"/>
								</svg>									
							</div>
						</div>
					</div>
				</div>
				<div class="library_slider swiper" data-aos="fade-up">
					<div class="swiper-wrapper">
						<?php
						$placeholderImages = [
							'frontend/img/temp/library_slider_item_1.jpg',
							'frontend/img/temp/library_slider_item_2.jpg',
							'frontend/img/temp/library_slider_item_3.jpg',
							'frontend/img/temp/library_slider_item_4.jpg'
						];
						if (!empty($remissionItems)):
							foreach ($remissionItems as $i => $item):
								$imgSrc = !empty($item['image']) ? '/' . ltrim($item['image'], '/') : $placeholderImages[$i % 4];
								$pdfUrl = !empty($item['pdf_path']) ? '/' . ltrim($item['pdf_path'], '/') : '';
								$titleEsc = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
						?>
						<div class="swiper-slide">
							<div class="library_slider_item">
								<div class="library_slider_item_image">
									<img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo $titleEsc; ?>">
								</div>
								<div class="library_slider_item_content d_flex f_direction_column j_content_between">
									<div class="library_slider_item_title">
										<?php if ($pdfUrl): ?>
										<a href="<?php echo htmlspecialchars($pdfUrl); ?>" target="_blank" rel="noopener"><?php echo $titleEsc; ?></a>
										<?php else: ?>
										<span><?php echo $titleEsc; ?></span>
										<?php endif; ?>
									</div>
									<?php if ($pdfUrl): ?>
									<div class="library_slider_item_btn">
										<a href="<?php echo htmlspecialchars($pdfUrl); ?>" target="_blank" rel="noopener" class="btn btn_blue">Читать</a>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php
							endforeach;
						else:
						?>
						<div class="swiper-slide">
							<div class="library_slider_item">
								<div class="library_slider_item_image">
									<img src="frontend/img/temp/library_slider_item_1.jpg" alt="">
								</div>
								<div class="library_slider_item_content d_flex f_direction_column j_content_between">
									<div class="library_slider_item_title">
										<span>Материалы скоро появятся</span>
									</div>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="register">
			<div class="container">
				<div class="register_bk d_flex f_wrap" data-aos="fade-up">
					<div class="register_left">
						<div class="register_title"><strong>Зарегистрируйтесь</strong>, станьте частью проекта Синапс Онлайн для врачей <strong>и получите в подарок на почту памятку медицинского юриста И. О. Печерея</strong></div>
						<div class="register_profile d_flex">
							<div class="register_profile_image">
								<img src="frontend/img/temp/register_profile_image.jpg" alt=""/>
							</div>
							<div class="register_profile_title">Медицинский юрист И. О. Печерей подготовил для вас краткую и практичную шпаргалку, которая поможет сориентироваться в сложной ситуации.</div>
						</div>
					</div>
					<div class="register_right">
						<!-- кастомная форма  -->
						<!-- <div class="register_form">
							<form id="form-register">
								<div class="form_fields">
									<div class="form_message" style="display:none; margin-bottom:1rem; padding:0.75rem; border-radius:4px;"></div>
									<div class="row row_form_fields d_flex f_wrap">
										<div class="col col-1">
											<div class="form_field form_field__input">
												<input type="text" name="surname" placeholder="Фамилия"/>
											</div>
										</div>
										<div class="col col-2">
											<div class="form_field form_field__input">
												<input type="text" name="name" placeholder="Имя"/>
											</div>
										</div>
										<div class="col col-2">
											<div class="form_field form_field__input">
												<input type="text" name="patronymic" placeholder="Отчество"/>
											</div>
										</div>
										<div class="col col-1">
											<div class="form_field form_field__select">
												<select name="specialty" placeholder="Специальность">
													<option value="">Специальность</option>
													<option value="Вариант 1">Вариант 1</option>
													<option value="Вариант 2">Вариант 2</option>
													<option value="Вариант 3">Вариант 3</option>
													<option value="Вариант 4">Вариант 4</option>
													<option value="Вариант 5">Вариант 5</option>
												</select>
											</div>
										</div>
										<div class="col col-2">
											<div class="form_field form_field__input">
												<input type="tel" name="phone" placeholder="Телефон"/>
											</div>
										</div>
										<div class="col col-2">
											<div class="form_field form_field__input">
												<input type="email" name="email" placeholder="Email"/>
											</div>
										</div>
										<div class="col col-1">
											<div class="form_field form_field__input">
												<input type="text" name="city" placeholder="Город"/>
											</div>
										</div>
										<div class="col col-1">
											<div class="form_field form_field__checkboxes">
												<label class="form_field_checkbox">
													<input type="checkbox" name="consent_personal" value="1"/>
													<span>Согласен(а) на обработку <a href="#">персональных данных</a></span>
												</label>
												<label class="form_field_checkbox">
													<input type="checkbox" name="consent_ads" value="1"/>
													<span>Согласен(а) на получение <a href="#">рекламной информации</a></span>
												</label>
											</div>
										</div>
										<div class="col col-1">
											<div class="form_field form_field__submit d_flex a_items_center">
												<div class="form_field_submit">
													<button type="submit" class="btn btn_green">Зарегистрироваться</button>
												</div>
												<div class="form_field_privacy">Нажимая на кнопку «Зарегистрироваться», я подтверждаю, что являюсь специалистом здравоохранения.</div>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div> -->
						<!-- форма виджет  -->
						<iframe src="https://pxl.synapseonline.ru/form?form=aSPyRtbdHcVmai9i4JtChQKbwUYSid3q7mZyb2CCp8e6RRHMHZcpSEcNjr7K8iDP8yENYVrCMrAFfQWezF5hWPsa&iframe=1" frameborder="0" name="ak-form-aSPyRtbdHcVmai9i4JtChQKbwUYSid3q7mZyb2CCp8e6RRHMHZcpSEcNjr7K8iDP8yENYVrCMrAFfQWezF5hWPsa" class="ak-form" width="100%" sandbox="allow-same-origin allow-forms allow-scripts allow-popups allow-popups-to-escape-sandbox allow-top-navigation"></iframe>
					</div>
				</div>
			</div>
		</div>
		<footer id="footer" class="footer">
			<div class="footer_top">
				<div class="container">
					<div class="footer_library">
						<div class="footer_library_title">Ссылки:</div>
						<div class="footer_library_list">
							<ol>
								<li>Котлуков В. К. и др. Атопический дерматит в детском возрасте //Медицинский совет, 2015. № 1. С. 60-65.</li>
								<li>Ревякина В. А. Энтеросорбенты в комплексной терапии атопического дерматита у детей //Эффективная фармакотерапия, 2010. № 8. С. 14-17</li>
								<li>Санто М. и др. Воздействие на ось кишечник–кожа — пробиотики как новые инструменты для лечения кожных заболеваний? // Экспериментальная дерматология. 2019; 28 (11): 1210–1218.</li>
								<li>Овсянников Д. Ю. Дисбактериоз кишечника у детей: этиология, клиническое значение, диагностические критерии, современные методы коррекции //Эффективная фармакотерапия, 2011. № 28. С. 10-19.</li>
								<li>Успенская Ю. Б. Хронические заболевания кожи через призму патологии желудочно-кишечного тракта //Эффективная фармакотерапия, 2016. Т. 30. С. 34-36</li>
								<li>Инструкция по медицинскому применению лекарственного препарата Лактофильтрум. Рег. уд. №: ЛП-008424</li>
							</ol>
						</div>
					</div>
				</div>
				<div class="footer_for_specialist">
					<img src="frontend/img/svg/footer_specialist.svg" alt=""/>
				</div>
			</div>
			<div class="footer_bottom">
				<div class="container container--small">
					<div class="footer_logo">
						<img src="frontend/img/svg/footer_logo.svg" alt=""/>
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
									Телефон: <a href="+74952211800">+7 (495) 221-18-00</a><br>
									Факс: <a href="tel:+74952211802">+7 (495) 221-18-02</a><br>
									E-mail: <a href="mailto:synapse@otcpharm.ru">synapse@otcpharm.ru</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <iframe src="https://pxl.synapseonline.ru/form?form=aSPyRtbdHcVmai9i4JtChQKbwUYSid3q7mZyb2CCp8e6RRHMHZcpSEcNjr7K8iDP8yENYVrCMrAFfQWezF5hWPsa&iframe=1" frameborder="0" name="ak-form-aSPyRtbdHcVmai9i4JtChQKbwUYSid3q7mZyb2CCp8e6RRHMHZcpSEcNjr7K8iDP8yENYVrCMrAFfQWezF5hWPsa" class="ak-form" width="100%" sandbox="allow-same-origin allow-forms allow-scripts allow-popups allow-popups-to-escape-sandbox allow-top-navigation"></iframe> -->
		</footer>
	</div>
	
	<script src=https://pxl.synapseonline.ru/form/js/embed_v1.js></script>
	<script type="text/javascript" src="frontend/js/jquery.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/swiper-bundle.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/choices.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/gsap.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/ScrollTrigger.min.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/aos.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/fancybox.umd.js?v=<?php echo $timestamp; ?>"></script>
	<script type="text/javascript" src="frontend/js/main.js?v=<?php echo $timestamp; ?>"></script>
	<script>
	(function() {
		var slider = document.querySelector('.podcasts_slider[data-podcasts-dynamic]');
		if (!slider) return;
		var wrapper = slider.querySelector('.swiper-wrapper');
		var sliderWrapper = slider.closest('.slider_wrapper');

		function url(path) {
			if (!path) return '#';
			if (path.indexOf('http') === 0) return path;
			return (window.location.origin + '/' + path.replace(/^\//, ''));
		}

		function esc(s) {
			if (s == null || s === '') return '';
			var div = document.createElement('div');
			div.textContent = s;
			return div.innerHTML;
		}
		function escAttr(s) {
			return esc(s).replace(/"/g, '&quot;');
		}

		fetch('php/api/podcasts.php')
			.then(function(r) { return r.json(); })
			.then(function(data) {
				wrapper.innerHTML = '';
				if (!data.success || !data.podcasts || data.podcasts.length === 0) {
					wrapper.innerHTML = '<div class="swiper-slide" style="padding: 2rem; text-align: center; color: #666;">Нет подкастов</div>';
				} else {
					data.podcasts.forEach(function(p) {
						var imgSrc = p.image ? url(p.image) : '';
						var authorImgSrc = p.author_photo ? url(p.author_photo) : '';
						var btnText = (p.button_link && p.button_link.trim()) ? p.button_link : 'Подробнее';
						var linkText = (p.additional_link && p.additional_link.trim()) ? p.additional_link : 'Получить памятку с кратким содержанием выпуска';
						var linkHref = p.extra_link ? url(p.extra_link) : '#';
						var slug = (p.slug && p.slug.trim()) ? p.slug : '';
						var btnHref = slug ? '/single/' + escAttr(slug) : '#';
						var slide = document.createElement('div');
						slide.className = 'swiper-slide';
						slide.innerHTML =
							'<div class="podcasts_slider_item">' +
								'<div class="podcasts_item_title">' + esc(p.title) + '</div>' +
								'<div class="podcasts_item_desc">' + esc(p.description || '') + '</div>' +
								'<div class="podcasts_item_preview">' +
									'<div class="podcasts_preview_image">' +
										(imgSrc ? '<img src="' + escAttr(imgSrc) + '" alt="' + escAttr(p.title) + '"/>' : '') +
									'</div>' +
								'</div>' +
								'<div class="podcasts_item_profile d_flex a_items_center">' +
									'<div class="podcasts_profile_image">' +
										(authorImgSrc ? '<img src="' + escAttr(authorImgSrc) + '" alt="' + escAttr(p.author || '') + '"/>' : '') +
									'</div>' +
									'<div class="podcasts_profile_info">' +
										'<div class="podcasts_profile_title">Спикер:</div>' +
										'<div class="podcasts_profile_desc">' + esc(p.author || '') + '</div>' +
									'</div>' +
								'</div>' +
								'<div class="podcasts_bottom d_flex a_items_center">' +
									'<div class="podcasts_btn">' +
										'<a href="' + btnHref + '" class="btn btn_green">' + esc(btnText) + '</a>' +
									'</div>' +
									'<div class="podcasts_note">' +
										'<a href="' + escAttr(linkHref) + '">' + esc(linkText) + '</a>' +
									'</div>' +
								'</div>' +
							'</div>';
						wrapper.appendChild(slide);
					});
				}

				var paginationEl = slider.querySelector('.swiper-pagination');
				if (!paginationEl) {
					paginationEl = document.createElement('div');
					paginationEl.className = 'swiper-pagination';
					slider.appendChild(paginationEl);
				}
				var prevEl = sliderWrapper ? sliderWrapper.querySelector('.slider_arr_left') : null;
				var nextEl = sliderWrapper ? sliderWrapper.querySelector('.slider_arr_right') : null;
				new Swiper(slider, {
					slidesPerView: 'auto',
					spaceBetween: 20,
					navigation: {
						prevEl: prevEl,
						nextEl: nextEl
					},
					pagination: {
						el: paginationEl,
						clickable: true,
						enabled: window.innerWidth <= 1024
					},
					breakpoints: {
						1025: {
							pagination: { enabled: false }
						}
					},
					on: {
						resize: function() {
							if (window.innerWidth <= 1024) {
								this.pagination.enable();
							} else {
								this.pagination.disable();
							}
						}
					}
				});
			})
			.catch(function(err) {
				console.error('Ошибка загрузки подкастов:', err);
				wrapper.innerHTML = '<div class="swiper-slide" style="padding: 2rem; text-align: center; color: #999;">Ошибка загрузки подкастов</div>';
			});
	})();
	</script>
</body>
</html>