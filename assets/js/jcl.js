let jclFunctions = {
    fadeIn: (element, duration = 500) => {
        console.log("fade in");
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
    },
    fadeOut: (element, duration = 500) => {
        console.log("fade out")
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
    },
    fadeToggle: (target, duration = 500) => {
        if (window.getComputedStyle(target).display === 'none') {
            return jclFunctions.fadeIn(target, duration);
        } else {
            return jclFunctions.fadeOut(target, duration);
        }
    },
    slideUp: (target, duration = 500) => {
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
    },
    slideDown: (target, duration = 500) => {
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
    },
    slideToggle: (target, duration = 500) => {
        if (window.getComputedStyle(target).display === 'none') {
            return jclFunctions.slideDown(target, duration);
        } else {
            return jclFunctions.slideUp(target, duration);
        }
    },
    showToggle: (target) => {
        if (window.getComputedStyle(target).display === 'none') {
            target.style.removeProperty('display');
        } else {
            target.style.display = 'none';
        }
    },
    siblings(el, filterType) {
        if (el.parentNode === null) {
            return [];
        }

        return [...el.parentElement.children].filter((ch) => {
            return el !== ch && ch.nodeName.toLowerCase() === filterType.toLowerCase();
        });
    },
};

function jsCategoriesListAnimate(clickedObj, listElements, options) {
    let toggleFunction;

    switch (options.fxIn) {
        case 'fadeIn':
            toggleFunction = jclFunctions.fadeToggle;
            break;
        case 'slideDown':
            toggleFunction = jclFunctions.slideToggle;
            break;
        default:
            toggleFunction = jclFunctions.showToggle;
            break;
    }

    listElements.forEach((listElement) => {
        toggleFunction(listElement);
    });


    if (clickedObj.parentNode.classList.contains('expanded')) {
        clickedObj.parentNode.classList.remove('expanded');
    } else {
        clickedObj.parentNode.classList.add('expanded');
    }
}

function jsCategoriesListClickEvent(options) {
    return function (e) {
        for (let target = e.target; target && target !== this; target = target.parentNode) {
            if (target.matches('.jcl_symbol')) {
                target.innerHTML = target.innerHTML.trim() === options.expSym ? options.conSym : options.expSym;

                const itemsToAnimate = jclFunctions.siblings(target, 'ul');
                if (itemsToAnimate.length) {
                    e.preventDefault();
                    jsCategoriesListAnimate(target, itemsToAnimate, options);
                    break;
                }
            }
        }
    };
}

/**
 * Assigns event listeners to the archive list.
 */
function jsArchiveListEvents() {
    document.querySelectorAll('.jcl_widget.legacy.preload').forEach((widget) => {
        widget.classList.remove('preload');

        const options = {
            fxIn: widget.dataset.fx_in,
            expSym: widget.dataset.ex_sym,
            conSym: widget.dataset.con_sym,
        };

        widget.addEventListener('click', jsCategoriesListClickEvent(options), false);
    });
}

/**
 * Event listener for clicks, it will start applying animation and expanding items
 * if clicked element belows to the widget.
 */
document.addEventListener('DOMContentLoaded', jsArchiveListEvents);
