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

			// ✨ 추가: 미세 스크롤 무시(데드존) + 레이아웃 변경 잠금
			const DELTA = 50;             // |currentY - lastScrollY| < 12px 이면 무시
			const FIX_OFFSET = 70;         // fix 적용 기준
			let layoutLock = false;        // 레이아웃 변경 중 잠깐 토글 금지
			let layoutLockTimer = null;

			const lockLayout = (ms = 280) => {
				layoutLock = true;
				clearTimeout(layoutLockTimer);
				layoutLockTimer = setTimeout(() => (layoutLock = false), ms);
			};

			// 높이가 바뀌는 영역 관찰(스와이퍼 래퍼 셀렉터로 교체하세요)
			// 예: .kv-swiper, .swiper, .main-visual 등
			const swiperEl = document.querySelector('.swiper'); // <-- 필요에 맞게 수정
			if (swiperEl && 'ResizeObserver' in window) {
				const ro = new ResizeObserver(() => lockLayout(300));
				ro.observe(swiperEl);
			}

			// 윈도우 리사이즈도 잠깐 잠금
			$win.on('resize', () => lockLayout(350));

			const updateScrollState = () => {
				const currentY = $win.scrollTop();

				// 레이아웃 변경 중엔 상태 고정 (값만 업데이트)
				if (layoutLock) {
					lastScrollY = currentY;
					ticking = false;
					return;
				}

				const diff = currentY - lastScrollY;

				// 미세 흔들림은 무시
				if (Math.abs(diff) < DELTA) {
					ticking = false;
					return;
				}

				if (currentY === 0) {
					$html.removeClass('fix out in');
				} else if (currentY > FIX_OFFSET) {
					$html.addClass('fix');

					if (diff > 0) {
						// 아래로 스크롤 → 헤더 숨김
						$html.removeClass('in');      // out만 남겨도 되고 필요시 $html.addClass('out')
					} else {
						// 위로 스크롤 → 헤더 노출
						$html.addClass('out in');     // in을 붙이고 싶으면 유지
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