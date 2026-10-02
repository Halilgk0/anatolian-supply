import { startMotion } from './motion';
import { initRail } from './rail';
import { initCategoryNav, initPegboard } from './catalog';
import { initParallax } from './parallax';
import { initMarquee } from './marquee';
import { initContours, initWordReveal } from './story';
import { initRoute } from './route';
import { initHeader } from './header';
import { initProductPage } from './product-page';

// The rail runs first: it sets its own height, which shifts everything measured after it.
initRail();
initPegboard();
initCategoryNav();
initParallax();
initMarquee();
initContours();
initWordReveal();
initRoute();
initHeader();
initProductPage();

startMotion();
