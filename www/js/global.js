const $win = $(window);
const $doc = $(document);
const $html = $('html');

const winW = () => $win.width();
const winH = () => $win.height();
const winSh = () => $win.scrollTop();

const controllers = {
	basic: {
		action() {
			let lastScrollY = 0;
			let ticking = false;

			const updateScrollState = () => {
				const currentY = $win.scrollTop();

				if (currentY === 0) {
					$html.removeClass('fix out in');
				} else if (currentY > 70) {
					$html.addClass('fix');

					if (currentY > lastScrollY) {
						$html.removeClass('in');
					} else {
						$html.addClass('out in');
					}
				}

				lastScrollY = currentY;
				ticking = false;
			};

			const requestScrollUpdate = () => {
				if (!ticking) {
					ticking = true;
					requestAnimationFrame(updateScrollState);
				}
			};

			$win.on('scroll load', requestScrollUpdate);

			$('._jsMnu').on('click',function(){
				$html.addClass('navOn');
				return false;
			});

			$('._nav .close').on('click',function(){
				$html.removeClass('navOn');
			});

			$('._nav .gnb > ul > li > a').on('click',function(){
				if ($(this).next('ul').length > 0) {
					const $li = $(this).closest('li');
					if ($li.hasClass('active')) {
						$li.removeClass('active');
					} else {
						$('._nav .gnb li').removeClass('active');
						$li.addClass('active');
					}
					return false;
				}
			});
		}
	},
	vis: {
		selector : '._vis',
		action() {
			swiper = new Swiper('._vis', {
				loop: true,
				effect: 'fade',
				speed: 1000,
				pagination: {
					el: '._vis .page',
					clickable: true,
					bulletElement: 'button',
					bulletClass: 'dot'
				},
				autoplay: {
					delay: 5000,
					disableOnInteraction: false
				},
			});

		}
	},
	hotWrap: {
		selector : '._hotWrap .roll',
		action() {

			var $wrapper = $('._hotWrap .swiper-wrapper');
			var $items = $wrapper.find('._item');
			var groupSize = 6;
			var $slide;

			// 기존 wrapper 비우기
			$wrapper.empty();

			// 6개씩 묶어서 .swiper-slide 생성
			$items.each(function(index){
				if(index % groupSize === 0){
					$slide = $('<div class="swiper-slide"></div>');
					$wrapper.append($slide);
				}
				$slide.append($(this));
			});

			swiper = new Swiper('._hotWrap .roll', {
				rewind: true,
				spaceBetween: 30,
				pagination: {
					el: '._hotWrap .page',
					clickable: true,
					bulletElement: 'button',
					bulletClass: 'dot'
				},
				autoplay: {
					delay: 5000,
					disableOnInteraction: false
				},
			});

		}
	},
};

function initControllers() {
	Object.values(controllers).forEach(controller => {
		if (controller.selector) {
			const el = document.querySelector(controller.selector);
			if (el) controller.action(el);
		} else if (controller.action) {
			controller.action();
		}
	});
}

document.addEventListener('DOMContentLoaded', () => {
	initControllers();
});

$win.on('load', () => {
	$html.addClass('load');
});