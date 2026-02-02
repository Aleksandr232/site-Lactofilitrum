$(document).ready(function() {
	
	var $page = $('html, body');
	$('a[href*="#"]:not(.open_modal)').on('click', function() {
		if($(this).attr("href") != "#") {
			$page.animate({
				scrollTop: $($.attr(this, 'href')).offset().top - $('.header').outerHeight() - 30
			}, 1000);
			return false;
		}
	});

	$('.menu_btn, .mobile_header_close').on('click', function(e) {
		e.preventDefault();
		$('.mobile_header_wrapper').toggleClass('active');
	});
	
	gsap.registerPlugin(ScrollTrigger);

	AOS.init({
		offset: -50
	});

	Fancybox.bind("[data-fancybox]", {});

	if($('.podcasts_slider').length != 0) {
		$('.podcasts_slider').each(function() {
			var slider = $(this);
			if (slider.data('podcasts-dynamic')) return; // подкасты подгружаются из API на index
			var sliderWrapper = slider.closest('.slider_wrapper');
			var paginationEl = slider.find('.swiper-pagination');
			
			if(paginationEl.length === 0) {
				paginationEl = $('<div class="swiper-pagination"></div>');
				slider.append(paginationEl);
			}
			
			new Swiper(slider[0], {
				slidesPerView: 'auto',
				spaceBetween: 20,
				navigation: {
					prevEl: sliderWrapper.find('.slider_arr_left')[0],
					nextEl: sliderWrapper.find('.slider_arr_right')[0],
				},
				pagination: {
					el: paginationEl[0],
					clickable: true,
					enabled: window.innerWidth <= 1024
				},
				breakpoints: {
					1025: {
						pagination: {
							enabled: false
						}
					}
				},
				on: {
					resize: function() {
						if(window.innerWidth <= 1024) {
							this.pagination.enable();
						} else {
							this.pagination.disable();
						}
					}
				}
			});
		});
	}

	if($('.healing').length != 0) {
		ScrollTrigger.matchMedia({
			"(min-width: 1025px)": function() {
				const healingBlock = document.querySelector('.healing');
				if (healingBlock) {
					healingBlock.addEventListener('mousemove', function(e) {
						const rect = healingBlock.getBoundingClientRect();
						const x = e.clientX - rect.left;
						const y = e.clientY - rect.top;
		
						const normalizedX = (x / rect.width) * 2 - 1;
						const normalizedY = (y / rect.height) * 2 - 1;
		
						gsap.to(".healing_bg", {
							x: normalizedX * 15,
							y: normalizedY * 15,
							duration: 0.5,
							ease: "power2.out"
						});
					});

					healingBlock.addEventListener('mouseleave', function() {
						gsap.to(".healing_bg", {
							x: 0,
							y: 0,
							duration: 0.8,
							ease: "power2.out"
						});
					});
				}
			}
		});
	}

	if($('.library_slider').length != 0) {
		$('.library_slider').each(function() {
			var slider = $(this);
			var sliderWrapper = slider.closest('.slider_wrapper');
			var paginationEl = slider.find('.swiper-pagination');
			
			if(paginationEl.length === 0) {
				paginationEl = $('<div class="swiper-pagination"></div>');
				slider.append(paginationEl);
			}
			
			new Swiper(slider[0], {
				slidesPerView: window.innerWidth <= 1024 ? 'auto' : 3,
				spaceBetween: 20,
				navigation: {
					prevEl: sliderWrapper.find('.slider_arr_left')[0],
					nextEl: sliderWrapper.find('.slider_arr_right')[0],
				},
				pagination: {
					el: paginationEl[0],
					clickable: true,
					enabled: window.innerWidth <= 1024
				},
				breakpoints: {
					1025: {
						slidesPerView: 3,
						pagination: {
							enabled: false
						}
					},
					0: {
						slidesPerView: 'auto'
					}
				},
				on: {
					resize: function() {
						if(window.innerWidth <= 1024) {
							this.params.slidesPerView = 'auto';
							this.update();
							this.pagination.enable();
						} else {
							this.params.slidesPerView = 3;
							this.update();
							this.pagination.disable();
						}
					}
				}
			});
		});
	}

	if($('.form_field__select select').length != 0) {
		$('.form_field__select select').each(function() {
			new Choices(this, {
				searchEnabled: false,
				itemSelectText: '',
				shouldSort: false
			});
		});
	}

	// Обработчик формы регистрации (POST на php/api/clients.php)
	$('#form-register').on('submit', function(e) {
		e.preventDefault();
		var $form = $(this);
		var $btn = $form.find('button[type="submit"]');
		var $msg = $form.find('.form_message');

		var data = {
			surname: $form.find('[name="surname"]').val().trim(),
			name: $form.find('[name="name"]').val().trim(),
			patronymic: $form.find('[name="patronymic"]').val().trim(),
			specialty: $form.find('[name="specialty"]').val().trim(),
			phone: $form.find('[name="phone"]').val().trim(),
			email: $form.find('[name="email"]').val().trim(),
			city: $form.find('[name="city"]').val().trim(),
			consent_personal: $form.find('[name="consent_personal"]').is(':checked'),
			consent_ads: $form.find('[name="consent_ads"]').is(':checked')
		};

		$msg.hide().removeClass('form_message--success form_message--error');
		$btn.prop('disabled', true);

		$.ajax({
			url: 'php/api/clients.php',
			method: 'POST',
			contentType: 'application/json',
			data: JSON.stringify(data),
			success: function(res) {
				if (res.success) {
					$msg.addClass('form_message--success').css('background', '#d4edda').css('color', '#155724').html(res.message || 'Регистрация успешно завершена').show();
					$form[0].reset();
					if (typeof Choices !== 'undefined') {
						$form.find('.form_field__select select').each(function() {
							var choices = Choices.getInstance(this);
							if (choices) choices.setChoiceByValue('');
						});
					}
				} else {
					$msg.addClass('form_message--error').css('background', '#f8d7da').css('color', '#721c24').html(res.message || 'Произошла ошибка').show();
				}
			},
			error: function(xhr) {
				var res = {};
				try { res = xhr.responseJSON || JSON.parse(xhr.responseText || '{}'); } catch (e) {}
				$msg.addClass('form_message--error').css('background', '#f8d7da').css('color', '#721c24').html(res.message || 'Ошибка отправки. Попробуйте позже.').show();
			},
			complete: function() {
				$btn.prop('disabled', false);
			}
		});
	});

	if($('.scheme_basic_image').length != 0) {
		$('.scheme_basic_image').on('mouseenter', function() {
			$(this).closest('.scheme_item').addClass('active');
		});

		$('.scheme_item').on('mouseleave', function() {
			$(this).removeClass('active');
		});
	}

	if ($('.mission').length != 0) {
		ScrollTrigger.matchMedia({
			"(min-width: 1025px)": function() {
				gsap.set('.mission_col_left', {
					y: -200,
					opacity: 0
				});
				gsap.set('.mission_col_right', {
					y: 200,
					opacity: 0
				});
				
				const rotationAngle = -45;
				
				gsap.set('.mission', {
					display: "flex",
					alignItems: "flex-start",
					justifyContent: "center",
					minHeight: "auto"
				});

				const tl = gsap.timeline({
					scrollTrigger: {
						trigger: ".mission",
						start: "top top",
						end: "+=2000",
						scrub: 1,
						pin: true
					}
				});

				tl.to('.mission', {
					alignItems: "center",
					minHeight: "100vh",
					duration: 0,
					ease: "none"
				}, 0)
				.to(".mission_col_left", {
					y: 0,
					opacity: 1,
					ease: "none",
					duration: 1
				}, 0)
				
				.to(".mission_col_right", {
					y: 0,
					opacity: 1,
					ease: "none",
					duration: 1
				}, 0)

				.to(".mission_circle", {
					rotation: rotationAngle,
					duration: 1,
					ease: "none",
					transformOrigin: "center center"
				}, 0)

				.to(".mission_circle_image_1", {
					rotation: -1 * rotationAngle,
					duration: 1,
					ease: "none"
				}, 0)

				.to(".mission_circle_image_2", {
					rotation: -1 * rotationAngle,
					duration: 1,
					ease: "none"
				}, 0)

				.to(".mission_circle_inside", {
					rotation: -1 * rotationAngle,
					duration: 1,
					ease: "none"
				}, 0)

				.to({}, { duration: 0.1 });
			},
			"(max-width: 1024px)": function() {
				gsap.set('.mission_col_left, .mission_col_right', {
					clearProps: "all"
				});
			}
		});
	}
	
	if ($('.about').length != 0) {
		ScrollTrigger.matchMedia({
			"(min-width: 1171px)": function() {
				gsap.set('.about_col_left, .about_col_right', {
					y: 150,
					opacity: 0
				});
				gsap.set('.about_bg_image', {
					opacity: 0
				});
				gsap.set('.about_image-2_1', {
					x: 0,
					y: 0
				});
				gsap.set('.about_image-2_2', {
					right: -20,
					bottom: 50,
					width: 189,
					height: 189
				});
			
				const tl = gsap.timeline({
					scrollTrigger: {
						trigger: ".about",
						start: "top top",
						end: "+=1500",
						scrub: 1,
						pin: true
					}
				});
			
				tl.to(".about_items", {
					y: -200,
					opacity: 0,
					duration: 1
				})
				.to(".about_bg_image", {
					opacity: 1,
					duration: 1
				}, "-=0.5")
			
				.to(".about_image_product", {
					rotation: 5,
					duration: 1,
					transformOrigin: "center center"
				}, "-=0.5") 
			
				.to(".about_image-2_1", {
					left: -30,
					top: 20,
					duration: 1,
					ease: "power2.out",
					transformOrigin: "center center"
				}, "<")
			
				.to(".about_image-2_2", {
					right: -50,
					bottom: 25,
					width: 250,
					height: 250,
					duration: 1,
					ease: "power2.out"
				}, "<")
			
				.fromTo(".about_col_left", 
					{ y: 150, opacity: 0 }, 
					{ y: 0, opacity: 1, duration: 1 }, 
					"<"
				)
				.fromTo(".about_col_right", 
					{ y: 100, opacity: 0 }, 
					{ y: -25, opacity: 1, duration: 1 }, 
					"<"
				);

				const aboutBlock = document.querySelector('.about');
				if (aboutBlock) {
					aboutBlock.addEventListener('mousemove', function(e) {
						const rect = aboutBlock.getBoundingClientRect();
						const x = e.clientX - rect.left;
						const y = e.clientY - rect.top;
			
						const normalizedX = (x / rect.width) * 2 - 1;
						const normalizedY = (y / rect.height) * 2 - 1;
			
						gsap.to(".about_bg_image", {
							x: normalizedX * 15,
							y: normalizedY * 15,
							duration: 0.5,
							ease: "power2.out"
						});
			
						gsap.to(".about_bg_circle .about_image", {
							x: normalizedX * 20,
							y: normalizedY * 20,
							duration: 0.5,
							ease: "power2.out"
						});
					});
			
					aboutBlock.addEventListener('mouseleave', function() {
						gsap.to(".about_bg_image, .about_bg_circle .about_image", {
							x: 0,
							y: 0,
							duration: 0.8,
							ease: "power2.out"
						});
					});
				}
			},
			"(max-width: 1170px)": function() {
				gsap.set('.about_col_left, .about_col_right, .about_bg_image, .about_image-2_1, .about_image-2_2', {
					clearProps: "all"
				});
			}
		});
	}


});