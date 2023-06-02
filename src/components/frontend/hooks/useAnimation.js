export default function useAnimation( effect ) {
	const fadeIn = (element, duration = 500) => {
        element.style.removeProperty('display');
        let display = window.getComputedStyle(element).display;

        if (display === 'none')
            display = 'block';

        element.style.display = display;
        element.style.opacity = 0;
        var last = +new Date();
        var tick = function () {
            element.style.opacity = +element.style.opacity + (new Date() - last) / duration;
            last = +new Date();
            if (+element.style.opacity < 1) {
                (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
            }
        };
        tick();
    };

	const fadeOut = (element, duration = 500) => {
        element.style.display = '';
        element.style.opacity = 1;
        var last = +new Date();
        var tick = function () {
            element.style.opacity = Number(+element.style.opacity - (new Date() - last) / duration).toFixed(4);
            last = +new Date();
            if (-element.style.opacity <= 0) {
                (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
            } else {
                element.style.display = 'none';
            }
        };
        tick();
    };

	const fadeToggle = (target, duration = 500) => {
        if (window.getComputedStyle(target).display === 'none') {
            return fadeIn(target, duration);
        } else {
            return fadeOut(target, duration);
        }
    };

	const slideUp = (target, duration = 500) => {
        target.style.transitionProperty = 'height, margin, padding';
        target.style.transitionDuration = duration + 'ms';
        target.style.boxSizing = 'border-box';
        target.style.height = target.offsetHeight + 'px';
        target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        window.setTimeout(() => {
            target.style.display = 'none';
            target.style.removeProperty('height');
            target.style.removeProperty('padding-top');
            target.style.removeProperty('padding-bottom');
            target.style.removeProperty('margin-top');
            target.style.removeProperty('margin-bottom');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
        }, duration);
    };

	const slideDown = (target, duration = 500) => {
        target.style.removeProperty('display');
        let display = window.getComputedStyle(target).display;

        if (display === 'none')
            display = 'block';

        target.style.display = display;
        let height = target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        target.offsetHeight;
        target.style.boxSizing = 'border-box';
        target.style.transitionProperty = "height, margin, padding";
        target.style.transitionDuration = duration + 'ms';
        target.style.height = height + 'px';
        target.style.removeProperty('padding-top');
        target.style.removeProperty('padding-bottom');
        target.style.removeProperty('margin-top');
        target.style.removeProperty('margin-bottom');
        window.setTimeout(() => {
            target.style.removeProperty('height');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
        }, duration);
    };

    const slideToggle = (target, duration = 500) => {
        if (window.getComputedStyle(target).display === 'none') {
            return slideDown(target, duration);
        } else {
            return slideUp(target, duration);
        }
    };

	const showToggle =  (target) => {
        if (window.getComputedStyle(target).display === 'none') {
            target.style.removeProperty('display');
        } else {
            target.style.display = 'none';
        }
    };

	let toggleFunction;

	switch ( effect ) {
		case 'fade':
			toggleFunction = fadeToggle;
			break;
		case 'slide':
			toggleFunction = slideToggle;
			break;
		default:
			toggleFunction = showToggle;
			break;
	}

    return toggleFunction;
}
