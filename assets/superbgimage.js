
var SuperBGImage = new Class({

    Implements: [Options],
    Binds: ['scrollIE6', 'load', 'preload', 'startSlideShow', 'stopSlideshow', 'nextSlide', 'prevSlide', 'resize', 'calcSize', 'showImage', 'loadImage', 'transition'],

    options: {
        id: 'superbgimage', // id for the containter
        z_index: -1, // z-index for the container
        inlineMode: 0, // 0-resize to browser size, 1-do not resize to browser-size
        showimage: 1, // number of first image to display
        vertical_center: 1, // 0-align top, 1-center vertical
        transition: 1, // 0-none, 1-fade, 2-slide down, 3-slide left, 4-slide top, 5-slide right, 6-blind horizontal, 7-blind vertical, 90-slide right/left, 91-slide top/down
        transitionout: 1, // 0-no transition for previous image, 1-transition for previous image
        randomtransition: 0, // 0-none, 1-use random transition (0-7)
        showtitle: 0, // 0-none, 1-show title
        slideshow: 0, // 0-none, 1-autostart slideshow
        slide_interval: 5000, // interval for the slideshow
        randomimage: 0, // 0-none, 1-random image
        speed: 600, // animation speed
        preload: 1, // 0-none, 1-preload images
        onShow: null, // function-callback show image
        onClick: null, // function-callback click image
        onHide: null, // function-callback hide image
        onMouseenter: null, // function-callback mouseenter
        onMouseleave: null, // function-callback mouseleave
        onMousemove: null // function-callback mousemove
	},

	inAnimation: false,
	slideshowActive: false,
	imgIndex: 1,
	imgActual: 1,
	imgLast: -1,
	imgSlide: 0,
	interval: 0,
	preload: 0,
	direction: 0,
	max_randomtrans: 7,
	lasttrans: -1,
	isIE6: false,
	firstLoaded: false,

    initialize: function(element, options) {

        this.setOptions(options);

        this.element = document.getElement(element);
        this.container = document.id(this.options.id);

		// prepend new/existing div with id from options to body
		if (!this.container) {
			this.container = new Element('div', {id: this.options.id});
		}

		document.id(document.body).grab(this.container, 'top');

		// set required css options
		this.container.setStyles({'display':'none', 'overflow':'hidden', 'z-index':this.options.z_index});
		// set required css options for fullscreen mode
		if (this.options.inlineMode === 0) {
			this.container.setStyles({'position':'fixed', 'width':'100%', 'height':'100%', 'top':0, 'left':0});
		}

		// reload true? remove all images
		if (this.options.reload) {
			this.container.getElements('img').dispose();
		}

		// hide all images, set position absolute
		this.container.getElements('img').fade('hide').setStyle('position', 'absolute');

		// add rel-attribute with index to all existing images
		this.container.getChildren('img').forEach((function(el) {
			el.set('rel', this.imgIndex++);
			// clear title-attribute
			if (!this.options.showtitle) {
				el.set('title', '');
			}
		}).bind(this));

		// add rel-attribute with index to all links
		this.element.getChildren('a').forEach((function(el, index) {
			// add click-event to links, add class for preload
			el.set('rel', this.imgIndex++).addEvent('click', (function() {
				this.showImage(++index);
				return false;
			}).bind(this)).addClass('preload');
		}).bind(this));

		// fix total counter
		this.imgIndex--;

		// bind load-event to show 1st image on document load
		window.addEvent('load', this.load);

		// bind resize-event to resize actual image
		window.addEvent('resize', this.resize);

		// fix for IE6
		this.isIE6 = /msie|MSIE 6/.test(navigator.userAgent);
		if (this.isIE6 && (this.options.inlineMode === 0)) {
			this.container.setStyles({'position':'absolute', 'width':window.getSize().x, 'height':window.getSize().y});
			window.addEvent('scroll', this.scrollIE6);
		}

		// reload true? show new image-set
		if (this.options.reload) {
			this.load();
		}

		return this;
    },


    // fix for IE6, handle scrolling-event
	scrollIE6: function() {

		// set top of the container
		this.container.setStyle('top', document.documentElement.scrollTop + 'px');
	},

	// handle load-event, show 1st image
	load: function() {

		// show container only if images/links exist
		if ((this.container.getChildren('img').length > 0) || (this.element.getChildren('a').length > 0)) {
			this.container.setStyle('display', 'block');
		}

		// 1st image to display set in options?
		if ((typeof this.options.showimage != 'undefined') && (this.options.showimage >= 0)) {
			this.imgActual = this.options.showimage;
		}

		// display random image?
		if (this.options.randomimage === 1) {
			this.imgActual = (1 + parseInt(Math.random() * (this.imgIndex - 1 + 1), 10));
		}

		// display 1st image
		this.showImage(this.imgActual);
	},

	// timer-function for preloading images
	preload: function() {

		// clear timer
		clearInterval(this.preloadTimer);

		// preload only if first image is loaded and linked images exist
		if (!this.firstLoaded && (this.element.getChildren('a').length > 0)) {
			this.preloadTimer = setInterval(this.preload, 111);
			return;
		}

		// get first image that is not loaded
		var el = this.element.getFirst('a.preload');

		if (!el) return;

		// get image index and title
		var imgrel = el.get('rel');
		var imgtitle = el.get('title');

		// preload image, set rel and title, prepend image to container, remove preload class
		var img = Asset.image(el.get('href'), {
    		onLoad: (function() {
    			img.setStyle('position', 'absolute').fade('hide');
    			if (this.container.getChildren('img' + "[rel='" + imgrel + "']").length === 0) {
    				img.set('rel', imgrel);
    				if (this.options.showtitle === 1) {
    					img.set('title', imgtitle);
    				}
    				this.container.grab(img, 'top');
    			}
    		}).bind(this)
		});

		// set timer to preload next image
		this.preloadTimer = setInterval(this.preload, 111);

		el.removeClass('preload')
	},

	// start slideshow
	startSlideShow: function() {

		// save active image
		this.imgSlide = this.imgActual;

		// clear previous timer
		if (this.interval !== 0) {
			clearInterval(this.interval);
		}

		// set timer and switch
		this.interval = setInterval(this.nextSlide, this.options.slide_interval);
		this.slideshowActive = true;

		return false;
	},

	// stop slideshow
	stopSlideShow: function() {

		// clear timer, set switch
		clearInterval(this.interval);
		this.slideshowActive = false;

		return false;
	},

	// next slide
	nextSlide: function() {

		// animation is running?
		if (this.inAnimation) return false;

		// clear timer when slideshow is active
		if (this.slideshowActive) {
			clearInterval(this.preloadTimer);
		}

		// direction for transition 90+91
		this.direction = 0;

		// index to next slide
		this.imgSlide++;
		if (this.imgSlide > this.imgIndex) {
			this.imgSlide = 1;
		}

		// display random images? index to random slide
		if (this.options.randomimage === 1) {
			this.imgSlide = (1 + parseInt(Math.random() * (this.imgIndex - 1 + 1), 10));
			while (this.imgSlide === this.imgLast) {
				this.imgSlide = (1 + parseInt(Math.random() * (this.imgIndex - 1 + 1), 10));
			}
		}

		// set actual index
		this.imgActual = this.imgSlide;

		// show image
		return this.showImage(this.imgActual);
	},

	// previous slide
	prevSlide: function() {

		// animation is running?
		if (this.inAnimation) return false;

		// direction for transition 90+91
		this.direction = 1;

		// index to previous slide
		this.imgSlide--;
		if (this.imgSlide < 1) {
			this.imgSlide = this.imgIndex;
		}

		// display random images? index to random slide
		if (this.options.randomimage === 1) {
			this.imgSlide = (1 + parseInt(Math.random() * (this.imgIndex - 1 + 1), 10));
			while (this.imgSlide === this.imgLast) {
				this.imgSlide = (1 + parseInt(Math.random() * (this.imgIndex - 1 + 1), 10));
			}
		}

		// set actual index
		this.imgActual = this.imgSlide;

		// show image
		return this.showImage(this.imgActual);

	},

	// handle resize-event, resize active image
	resize: function() {

		// get active image
		var thisimg = this.container.getElement('img.activeslide');

		// calculate size and position
		var dimensions = this.calcSize(thisimg.getSize().x, thisimg.getSize().y);
		var newwidth = dimensions[0];
		var newheight = dimensions[1];
		var newleft = dimensions[2];
		var newtop = dimensions[3];

		// set new width/height
		thisimg.setStyle('width', newwidth + 'px');
		thisimg.setStyle('height', newheight + 'px');

		// fix for IE6
		if (this.isIE6 && (this.options.inlineMode === 0)) {
			this.container.setStyles({'width':newwidth, 'height':newheight});
			thisimg.setStyle('width', newwidth);
			thisimg.setStyle('height', newheight);
		}

		// set new left position
		thisimg.setStyle('left', newleft + 'px');

		// set new top when option vertical_center is on, otherwise set to 0
		if (this.options.vertical_center === 1){
			thisimg.setStyle('top', newtop + 'px');
		} else {
			thisimg.setStyle('top', '0px');
		}
	},

	// calculate image size, top and left position
	calcSize: function(imgw, imgh) {

		// get browser dimensions
		var browserwidth = window.getSize().x;
		var browserheight = window.getSize().y;

		// use container dimensions when inlinemode is on
		if (this.options.inlineMode === 1) {
			browserwidth = this.container.getSize().x;
			browserheight = this.container.getSize().y;
		}

		// calculate ratio
		var ratio = imgh / imgw;

		// calculate new size
		var newheight = 0; var newwidth = 0;
		if ((browserheight / browserwidth) > ratio) {
			newheight = browserheight;
			newwidth = Math.round(browserheight / ratio);
		} else {
			newheight = Math.round(browserwidth * ratio);
			newwidth = browserwidth;
		}

		// calculate new left and top position
		var newleft = Math.round((browserwidth - newwidth) / 2);
		var newtop = Math.round((browserheight - newheight) / 2);

		var rcarr = [newwidth, newheight, newleft, newtop];
		return rcarr;

	},

	// show image, call callback onHide
	showImage: function(img) {

		this.imgActual = img;

		// exit when already active image
		if (this.container.getElement('img.activeslide') && this.container.getElement('img.activeslide').get('rel') === this.imgActual) {
			return false;
		}

		// exit when animation is running, otherwise set switch
		if (this.inAnimation) {
			return false;
		} else {
			this.inAnimation = true;
		}

		// get source and title from link
		var imgsrc = ''; var imgtitle = '';
		if (this.container.getChildren('img' + "[rel='" + this.imgActual + "']").length === 0) {
			imgsrc = this.element.getFirst('a' + "[rel='" + this.imgActual + "']").get('href');
			imgtitle = this.element.getFirst('a' + "[rel='" + this.imgActual + "']").get('title');
		// otherwise get source from image
		} else {
			imgsrc = this.container.getFirst('img' + "[rel='" + this.imgActual + "']").get('src');
		}

		// callback function onHide
		if ((typeof this.options.onHide === 'function') && (this.options.onHide !== null) && (this.imgLast >= 0)) {
			this.options.onHide(this.imgLast);
		}

		// load the image, do selected transition
		this.loadImage(imgsrc, imgtitle);

		// set class activeslide for the actual link
		this.element.getElements('a').removeClass('activeslide');
		this.element.getElement('a' + "[rel='" + this.imgActual + "']").addClass('activeslide');

		// save image-index
		this.imgSlide = this.imgActual;
		this.imgLast = this.imgActual;

		return false;
	},

	// load image, show the image and perform the transition
	loadImage: function(imgsrc, imgtitle) {

		// load image, add image to container
		if (this.container.getChildren('img' + "[rel='" + this.imgActual + "']").length === 0) {
			var img = Asset.image(imgsrc, {
    			onLoad: (function() {
    				img.setStyle('position', 'absolute').fade('hide');
    				if (this.container.getChildren('img' + "[rel='" + this.imgActual + "']").length === 0) {
    					img.set('rel', this.imgActual);
    					if (this.options.showtitle === 1) {
    						img.setAttribute('title', imgtitle);
    					}
    					this.container.grab(img, 'top');
    				}
    				var thisimg = this.container.getFirst('img' + "[rel='" + this.imgActual + "']");
    				var dimensions = this.calcSize(img.width, img.height);
    				// perform the transition
    				this.transition(thisimg, dimensions);
    				// first image loaded?
    				if (!this.firstLoaded) {
    					// start slideshow?
    					if (this.options.slideshow === 1) {
    						this.startSlideShow();
    					}
    					// preload files when images are linked
    					if ((this.options.preload === 1) && (this.element.getChildren('a').length > 0)) {
    						this.preloadTimer = setInterval(this.preload, 250);
    					}
    				}
    				this.firstLoaded = true;
    			}).bind(this),
    			onError: (function() {
    				this.inAnimation = false;
    			}).bind(this)
    		});
		// image already loaded
		} else {
			var thisimg = this.container.getFirst('img' + "[rel='" + this.imgActual + "']");
			var dimensions = this.calcSize(thisimg.getSize().x, thisimg.getSize().y);
			// perform the transition
			this.transition(thisimg, dimensions);
			if (!this.firstLoaded) {
				// start slideshow?
				if (this.options.slideshow === 1) {
					this.startSlideShow();
				}
				// preload files when images are linked
				if ((this.options.preload === 1) && (this.element.getChildren('a').length > 0)) {
					this.preloadTimer = setInterval(this.preload, 250);
				}
				this.firstLoaded = true;
			}
		}

	},

	// perform the transition
	transition: function(thisimg, dimensions) {

		var newwidth = dimensions[0];
		var newheight = dimensions[1];
		var newleft = dimensions[2];
		var newtop = dimensions[3];

		// set new width, height and left position
		thisimg.setStyles({'width':newwidth + 'px', 'height':newheight + 'px', 'left':newleft + 'px'});

		// callbacks onClick, onMouseenter, onMouseleave, onMousemove
		if ((typeof this.options.onClick === 'function') && (this.options.onClick !== null)) {
			thisimg.removeEvents('click').addEvent('click', (function() { this.options.onClick(this.imgActual); }).bind(this));
		}
		if ((typeof this.options.onMouseenter === 'function') && (this.options.onMouseenter !== null)) {
			thisimg.removeEvents('mouseenter').addEvent('mouseenter', (function() { this.options.onMouseenter(this.imgActual); }).bind(this));
		}
		if ((typeof this.options.onMouseleave === 'function') && (this.options.onMouseleave !== null)) {
			thisimg.removeEvents('mouseleave').addEvent('mouseleave', (function() { this.options.onMouseleave(this.imgActual); }).bind(this));
		}
		if ((typeof this.options.onMousemove === 'function') && (this.options.onMousemove !== null)) {
			thisimg.removeEvents('mousemove').addEvent('mousemove', (function(e) { this.options.onMousemove(this.imgActual, e); }).bind(this));
		}

		// random transition
		if (this.options.randomtransition === 1) {
			var randomtrans = (0 + parseInt(Math.random() * (this.max_randomtrans - 0 + 1), 10));
			while (randomtrans === this.lasttrans) {
				randomtrans = (0 + parseInt(Math.random() * (this.max_randomtrans - 0 + 1), 10));
			}
			this.options.transition = randomtrans;
		}

		// set new top when option vertical_center is on, otherwise set to 0
		if (this.options.vertical_center === 1){
			thisimg.setStyle('top', newtop + 'px');
		} else {
			thisimg.setStyle('top', '0px');
		}

		// switch for transitionout
		var akt_transitionout = this.options.transitionout;
		// no transitionout for blind effect
		if ((this.options.transition === 6) || (this.options.transition === 7)) {
			akt_transitionout = 0;
		}

		// prepare last active slide for transition out/hide
		if (akt_transitionout === 1) {
			!this.container.getFirst('img.activeslide') || this.container.getFirst('img.activeslide').removeClass('activeslide').addClass('lastslide').setStyle('z-index', 0);
		} else {
			!this.container.getFirst('img.activeslide') || this.container.getFirst('img.activeslide').removeClass('activeslide').addClass('lastslideno').setStyle('z-index', 0);
		}

		// set z-index on new active image
		thisimg.setStyle('z-index', 1);

		// be sure transition is numeric
		this.options.transition = parseInt(this.options.transition, 10);
		this.lasttrans = this.options.transition;

		// no transition
		var theEffect = ''; var theDir = '';
		if (this.options.transition === 0) {
			thisimg.get('tween').chain((function() {
				if ((typeof this.options.onShow === 'function') && (this.options.onShow !== null)) this.options.onShow(this.imgActual);
				this.inAnimation = false;
				if (this.slideshowActive) {
					this.startSlideShow();
				}
			}).bind(this));
			thisimg.fade('show').addClass('activeslide');
		// transition fadeIn
		} else if (this.options.transition === 1) {
			thisimg.get('tween').chain((function() {
				if ((typeof this.options.onShow === 'function') && (this.options.onShow !== null)) this.options.onShow(this.imgActual);
				!this.container.getFirst('img.lastslideno') || this.container.getFirst('img.lastslideno').fade('hide').removeClass('lastslideno');
				this.inAnimation = false;
				if (this.slideshowActive) {
					this.startSlideShow();
				}
			}).bind(this));
			thisimg.set('tween', {duration:this.options.speed}).fade('in').addClass('activeslide');
		// other transitions slide and blind
		} else {
/*
			if (options.transition === 2) { theEffect = 'slide'; theDir = 'up'; }
			if (options.transition === 3) { theEffect = 'slide'; theDir = 'right'; }
			if (options.transition === 4) { theEffect = 'slide'; theDir = 'down'; }
			if (options.transition === 5) { theEffect = 'slide'; theDir = 'left'; }
			if (options.transition === 6) { theEffect = 'blind'; theDir = 'horizontal'; }
			if (options.transition === 7) { theEffect = 'blind'; theDir = 'vertical'; }
			if (options.transition === 90) {
				theEffect = 'slide'; theDir = 'left';
				if (this.direction === 1) {
					theDir = 'right';
				}
			}
			if (options.transition === 91) {
				theEffect = 'slide'; theDir = 'down';
				if (this.direction === 1) {
					theDir = 'up';
				}
			}
			// perform transition slide/blind, add class activeslide
			$(thisimg).show(theEffect, { direction: theDir }, options.speed, function() {
				if ((typeof options.onShow === 'function') && (options.onShow !== null)) options.onShow(this.imgActual);
				$('#' + options.id + ' img.lastslideno').hide(1, null).removeClass('lastslideno');
				this.inAnimation = false;
				if (this.slideshowActive) {
					$('#' + options.id).startSlideShow();
				}
			}).addClass('activeslide');
*/
		}

		// perform transition out
		if (akt_transitionout === 1) {
			// add some time to out speed
			var outspeed = this.options.speed;
			if (this.options.speed == 'slow') {
				outspeed = 600 + 200;
			} else if (this.options.speed == 'normal') {
				outspeed = 400 + 200;
			} else if (this.options.speed == 'fast') {
				outspeed = 400 + 200;
			} else {
				outspeed = this.options.speed + 200;
			}

			// no transition
			if (this.options.transition === 0) {
				!this.container.getFirst('img.lastslide') || this.container.getFirst('img.lastslide').fade('hide').removeClass('lastslide');
			// transition fadeIn
			} else if (this.options.transition == 1) {
				!this.container.getFirst('img.lastslide') || this.container.getFirst('img.lastslide').set('tween', {duration:outspeed}).fade('out').removeClass('lastslide');
			// other transitions slide and blind
			} else {
/*
				if (options.transition === 2) { theEffect = 'slide'; theDir = 'down'; }
				if (options.transition === 3) { theEffect = 'slide'; theDir = 'left'; }
				if (options.transition === 4) { theEffect = 'slide'; theDir = 'up'; }
				if (options.transition === 5) { theEffect = 'slide'; theDir = 'right'; }
				if (options.transition === 6) { theEffect = ''; theDir = ''; }
				if (options.transition === 7) { theEffect = ''; theDir = ''; }
				if (options.transition === 90) {
					theEffect = 'slide'; theDir = 'right';
					if (this.direction === 1) {
						theDir = 'left';
					}
				}
				if (options.transition === 91) {
					theEffect = 'slide'; theDir = 'up';
					if (this.direction === 1) {
						theDir = 'down';
					}
				}
				// perform transition slide/blind, add class activeslide
				$('#' + options.id + ' img.lastslide').hide(theEffect, { direction: theDir }, outspeed).removeClass('lastslide');
*/
			}
		// no transition out
		} else {
			this.container.getFirst('img.lastslide').fade('hide').removeClass('lastslide');
		}
	}
});