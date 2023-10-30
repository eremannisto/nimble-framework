class Navigation {

    constructor() {

        this.DEBUG      = false; // Set to true to enable debug mode
        this.OFFSET     = 20;    // The offset in pixels which after the navigation should be hidden    
        this.NAV_HEIGHT = 8;     // The height of the navigation in rem values
        this.MENU_WIDTH = 840;   // The media query width for the mobile menu

        this.body       = document.body;
        this.navigation = document.getElementById('navigation');
        this.items      = document.querySelectorAll('#navigation a.link');
        this.mobileMenu = document.getElementById('mobile-menu');
        this.menuItems  = document.querySelectorAll('#mobile-menu a.link');
        
        this.lastScrollPosition    = 0;
        this.lastMousePosition     = 0;
        this.currentScrollPosition = 0;
        this.currentMousePosition  = 0;

        this.touchScreen            = false;

        // Close mobile menu on load
        this.body.setAttribute('data-mobile-menu', 'closed');
        this.currentMenuStatus = this.body.getAttribute('data-mobile-menu');

        // Event listeners
        document.addEventListener('touchstart', () => this.touchScreen = true,  { passive: true });
        document.addEventListener('touchend',   () => this.touchScreen = false, { passive: true });
        window.addEventListener(  'scroll',     ()      => this.scroll());
        window.addEventListener(  'mousemove',  (event) => this.mouse(event));
        window.addEventListener(  'resize',     ()      => this.removeMenu());
        this.load();

        // Mutation observer
        const observer       = new MutationObserver(() => this.toggle());
        const observerConfig = { attributes: true };
        observer.observe(document.body, observerConfig);
        this.toggle();

        // If mobile menu button is clicked, toggle the menu
        const menuButton = document.querySelectorAll('[data-menu-toggle]');
        menuButton.forEach(button => {
            button.addEventListener('click', () => {
                this.toggleMenu();
            });
        });

        // When navigation item is clicked, close the menu
        this.menuItems.forEach(item => {
            item.addEventListener('click', () => {
                this.toggleMenu();
            });
        });
    }

    /**
     * Console logs the current state of the navigation component
     * as a table.
     * 
     * @returns {void}
     */
    debug() {
        if(this.DEBUG === false) return;
        console.table({
            'data-navigation-visible'       : this.navigation.getAttribute('data-navigation-visible'),
            'data-navigation-background'    : this.navigation.getAttribute('data-navigation-background'),
            'currentScrollPosition'         : this.currentScrollPosition,
            'lastScrollPosition'            : this.lastScrollPosition,
            'currentMousePosition'          : this.currentMousePosition,
            'lastMousePosition'             : this.lastMousePosition,
            'pageHeight'                    : document.body.scrollHeight,
            'touchScreen'                   : this.touchScreen
        });
    }

    /**
     * Toggles the visibility of the navigation component.
     * 
     * @param {string} bool 
     */
    toggleNavigation(bool) {
        this.navigation.setAttribute('data-navigation-visible', bool 
            ? 'true' : 'false');
    }

    /**
     * Toggles the background of the navigation based on the 
     * current scroll position.
     * 
     * @return {void}
     * Returns nothing.
     */
    toggleBackground() {
        this.navigation.setAttribute('data-navigation-background', 
            this.currentScrollPosition >= this.OFFSET
            ? 'true' : 'false'); 
    }

    /**
     * Toggles the mobile menu.
     * 
     * @returns {void}
     * Returns nothing.
     */
    toggleMenu(menuStatus) {

        if ( menuStatus === 'closed' || menuStatus === 'open' ) { this.currentMenuStatus = menuStatus; } 
        else { this.currentMenuStatus = (this.currentMenuStatus === 'closed' ) ? 'open' : 'closed'; }

        this.body.setAttribute('data-mobile-menu', this.currentMenuStatus);

        if(this.currentMenuStatus === 'open') {
            this.mobileMenu.setAttribute('data-menu-animation', 'fading-in');
            setTimeout(() => {
                this.mobileMenu.setAttribute('data-menu-animation', 'finished');
            }, 500);
        } 
        else if (this.currentMenuStatus === 'closed') {
            this.mobileMenu.setAttribute('data-menu-animation', 'fading-out');
            setTimeout(() => {
                this.mobileMenu.setAttribute('data-menu-animation', 'finished');
            }, 500);
        }
    }

    /**
     * Removes the mobile menu if the window width is greater than the
     * menu width.
     */
    removeMenu() {
        if(window.innerWidth >= this.MENU_WIDTH) {
            if(this.currentMenuStatus === 'open') this.toggleMenu();
        }
    }


    /**
     * Returns the height of the navigation bar in pixels.
     * 
     * @returns {number} 
     * The height of the navigation bar in pixels.
     */
    getNavigationHeight() {
        return this.NAV_HEIGHT * parseFloat(getComputedStyle(document.documentElement).fontSize);
    }

    /**
     * Determines if the mouse is currently over the navigation component.
     * 
     * @returns {boolean} 
     * True if the mouse is over the navigation, false otherwise.
     */
    isMouseOverNavigation() {
        return this.currentMousePosition >= this.getNavigationHeight();
    }

    /**
     * Checks if the device has a touch screen.
     * 
     * @returns {boolean} 
     * Returns true if the device has a touch screen, otherwise false.
     */
    isTouchScreen() {
        return  this.touchScreen = 'ontouchstart' in window || 
                navigator.maxTouchPoints 
                    ? true : false;
    }

    /**
     * Loads the navigation component by setting the current scroll position, 
     * toggling the navigation, and toggling the background.
     */
    load() {

        // Check if the device is a touch screen
        this.isTouchScreen();

        // Set the current scroll position and toggle the background
        this.currentScrollPosition = window.scrollY;
        this.toggleBackground();

        // If we have scrolled past the offset, and the mouse is not over the navigation,
        // and the current scroll position is greater than the last scroll position,
        // hide the navigation. Otherwise, show the navigation.
        this.currentScrollPosition <= this.lastScrollPosition ||
        this.currentScrollPosition <= this.OFFSET 
            ? this.toggleNavigation(true) 
            : this.toggleNavigation(false);


        this.debug();
    }

    /**
     * Handles the scrolling behavior of the navigation bar.
     */
    scroll(event) {

        // Check if the device is a touch screen
        this.isTouchScreen();

        // Update the current scroll position, and toggle the background
        this.currentScrollPosition = window.scrollY;
        this.toggleBackground();

        // If the mouse is over the navigation, exit the function.
        if(!this.touchScreen){
            if (this.lastMousePosition <= this.getNavigationHeight()){
                this.lastScrollPosition = this.currentScrollPosition;
                return;
            }
        }

        // If we have scrolled past the offset, and the mouse is not over the navigation,
        // and the current scroll position is greater than the last scroll position,
        // hide the navigation. Otherwise, show the navigation.
        this.currentScrollPosition <= this.lastScrollPosition || 
        this.currentScrollPosition <= this.OFFSET
            ? this.toggleNavigation(true) 
            : this.toggleNavigation(false);

        // Update the last scroll position
        this.debug(); // First call the debug function
        this.lastScrollPosition = this.currentScrollPosition;
    }

    /**
     * Handles mouse events for the navigation component.
     * 
     * @param {MouseEvent} event 
     * The mouse event object.
     */
    mouse(event) {

        // Check if the device is a touch screen
        this.isTouchScreen();

        // Update the current mouse position (and the scroll position) 
        // and toggle the background
        this.currentMousePosition  = event.clientY
        this.currentScrollPosition = window.scrollY;
        this.toggleBackground();

        // If we havent scrolled yet past the offset, exit the function:
        if (this.currentScrollPosition <= this.OFFSET || 
            this.isMouseOverNavigation()) {
                this.lastMousePosition = this.currentMousePosition;
                return;
        }

        // If the mouse is over the navigation or the current scroll position
        // is less than the offset, show the navigation; Otherwise, hide the navigation.
        this.currentMousePosition  <= this.getNavigationHeight() || 
        this.currentScrollPosition <= this.OFFSET
            ? this.toggleNavigation(true) 
            : this.toggleNavigation(false);

        // Update the last mouse position
        this.debug();
        this.lastMousePosition = this.currentMousePosition;
    }

    /**
     * Toggles the active class of the navigation items.
     * 
     * @returns {void}
     * Returns nothing.
     */
    toggle() {
        
        // This should first check if data-anchors exist on the page
        // If not, exit the function
        if (!document.querySelector('[data-anchor]')) return;
        
        this.items.forEach(item => {
            const href = item.getAttribute('href');
            const target = href.substring(1);
            const element = document.getElementById(target);

            if (!element.hasAttribute('data-anchor')) return;
            this.body.getAttribute('data-observing') === target
                ? item.classList.add('active')
                : item.classList.remove('active');
        });
    }
}

// Initialize the navigation
document.addEventListener('DOMContentLoaded', () => new Navigation());