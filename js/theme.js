(function($) {
    'use strict';
    
    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.querySelector('.theme-toggle-icon');
    
    // Check for saved theme preference
    const currentTheme = localStorage.getItem('lofygame-theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    if (currentTheme === 'dark') {
        themeIcon.textContent = '☀️';
    }
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('lofygame-theme', newTheme);
        
        themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
    });
    
    // Enhanced Fullscreen functionality
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const exitFullscreenBtn = document.getElementById('exit-fullscreen');
    const gameIframe = document.getElementById('game-iframe');
    const gameContainer = document.querySelector('.game-iframe-container');
    
    if (fullscreenBtn && gameIframe) {
        fullscreenBtn.addEventListener('click', function() {
            enterFullscreen();
        });
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.addEventListener('click', function() {
                exitFullscreen();
            });
        }
        
        // Double-click to enter fullscreen
        gameIframe.addEventListener('dblclick', function() {
            if (!document.fullscreenElement) {
                enterFullscreen();
            }
        });
    }
    
    function enterFullscreen() {
        // Try different fullscreen APIs
        const element = gameContainer || gameIframe;
        
        if (element.requestFullscreen) {
            element.requestFullscreen().then(() => {
                handleFullscreenEntered();
            }).catch(err => {
                console.log('Fullscreen request failed:', err);
                fallbackFullscreen();
            });
        } else if (element.mozRequestFullScreen) { // Firefox
            element.mozRequestFullScreen();
            handleFullscreenEntered();
        } else if (element.webkitRequestFullscreen) { // Chrome, Safari, Opera
            element.webkitRequestFullscreen();
            handleFullscreenEntered();
        } else if (element.msRequestFullscreen) { // IE/Edge
            element.msRequestFullscreen();
            handleFullscreenEntered();
        } else {
            // Fallback for browsers that don't support fullscreen API
            fallbackFullscreen();
        }
    }
    
    function handleFullscreenEntered() {
        gameIframe.classList.add('fullscreen-iframe');
        document.body.classList.add('fullscreen-active');
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.style.display = 'block';
        }
        
        if (fullscreenBtn) {
            fullscreenBtn.style.display = 'none';
        }
        
        // Focus the iframe for better interaction
        gameIframe.focus();
    }
    
    function fallbackFullscreen() {
        // Custom fullscreen implementation
        gameIframe.classList.add('fullscreen-iframe');
        document.body.classList.add('fullscreen-active');
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.style.display = 'block';
        }
        
        if (fullscreenBtn) {
            fullscreenBtn.style.display = 'none';
        }
        
        // Hide browser UI on mobile
        if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            // Request fullscreen on mobile
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            }
        }
    }
    
    function exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        
        handleFullscreenExited();
    }
    
    function handleFullscreenExited() {
        gameIframe.classList.remove('fullscreen-iframe');
        document.body.classList.remove('fullscreen-active');
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.style.display = 'none';
        }
        
        if (fullscreenBtn) {
            fullscreenBtn.style.display = 'block';
        }
    }
    
    // Listen for fullscreen change events
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            handleFullscreenExited();
        }
    });
    
    document.addEventListener('webkitfullscreenchange', function() {
        if (!document.webkitFullscreenElement) {
            handleFullscreenExited();
        }
    });
    
    document.addEventListener('mozfullscreenchange', function() {
        if (!document.mozFullScreenElement) {
            handleFullscreenExited();
        }
    });
    
    document.addEventListener('MSFullscreenChange', function() {
        if (!document.msFullscreenElement) {
            handleFullscreenExited();
        }
    });
    
    // ESC key to exit fullscreen
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && (document.body.classList.contains('fullscreen-active') || document.fullscreenElement)) {
            exitFullscreen();
        }
    });
    
    // Enhanced Game filtering with pagination support
    const categoryFilter = document.getElementById('category-filter');
    const tagFilter = document.getElementById('tag-filter');
    const gamesGrid = document.getElementById('games-grid');
    let currentPage = 1;
    
    if (categoryFilter && tagFilter && gamesGrid) {
        categoryFilter.addEventListener('change', function() {
            currentPage = 1; // Reset to first page
            filterGames();
        });
        
        tagFilter.addEventListener('change', function() {
            currentPage = 1; // Reset to first page
            filterGames();
        });
    }
    
    function filterGames(page = 1) {
        const category = categoryFilter ? categoryFilter.value : 'all';
        const tag = tagFilter ? tagFilter.value : 'all';
        
        // Show loading
        if (gamesGrid) {
            gamesGrid.innerHTML = '<div class="loading-container"><div class="loading"></div><p>Loading games...</p></div>';
        }
        
        $.ajax({
            url: lofygame_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_games',
                category: category,
                tag: tag,
                paged: page,
                nonce: lofygame_ajax.nonce
            },
            success: function(response) {
                if (gamesGrid) {
                    gamesGrid.innerHTML = response;
                    
                    // Re-initialize hover effects
                    initializeGameCards();
                    initializeMinimalVideoPreview();
                    
                    // Trigger custom event for other scripts
                    document.dispatchEvent(new CustomEvent('gamesLoaded'));
                }
            },
            error: function() {
                if (gamesGrid) {
                    gamesGrid.innerHTML = '<div class="no-games"><h2>Error</h2><p>Error loading games. Please try again.</p></div>';
                }
            }
        });
    }
    
    // Enhanced pagination handling
    function initializePagination() {
        // Handle pagination clicks
        $(document).on('click', '.games-pagination a.page-numbers', function(e) {
            e.preventDefault();
            
            const url = this.href;
            const urlParams = new URLSearchParams(new URL(url).search);
            const page = urlParams.get('paged') || this.textContent;
            
            // Update browser URL without reload
            if (history.pushState) {
                history.pushState(null, null, url);
            }
            
            // Load new page
            if (categoryFilter && tagFilter) {
                // AJAX filtering is active
                currentPage = parseInt(page);
                filterGames(currentPage);
            } else {
                // Regular pagination
                loadPage(page);
            }
            
            // Scroll to top of games grid
            const gamesSection = document.querySelector('.games-section');
            if (gamesSection) {
                gamesSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
        
        // Handle browser back/forward
        window.addEventListener('popstate', function(event) {
            location.reload(); // Simple reload for now, could be enhanced
        });
    }
    
    function loadPage(page) {
        const gamesContainer = document.querySelector('.games-grid-12-col');
        if (!gamesContainer) return;
        
        // Show loading
        gamesContainer.innerHTML = '<div class="loading-container"><div class="loading"></div><p>Loading games...</p></div>';
        
        // Get current URL and update page parameter
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('paged', page);
        
        // Fetch new page content
        fetch(currentUrl.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGamesGrid = doc.querySelector('.games-grid-12-col');
                const newPagination = doc.querySelector('.games-pagination');
                
                if (newGamesGrid) {
                    gamesContainer.innerHTML = newGamesGrid.innerHTML;
                    
                    // Update pagination
                    const currentPagination = document.querySelector('.games-pagination');
                    if (currentPagination && newPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }
                    
                    // Re-initialize
                    initializeGameCards();
                    initializeMinimalVideoPreview();
                    
                    // Trigger custom event
                    document.dispatchEvent(new CustomEvent('gamesLoaded'));
                }
            })
            .catch(error => {
                console.error('Error loading page:', error);
                gamesContainer.innerHTML = '<div class="no-games"><h2>Error</h2><p>Failed to load page. Please try again.</p></div>';
            });
    }
    
    // Initialize game card hover effects
    function initializeGameCards() {
        const gameCards = document.querySelectorAll('.game-card');
        
        gameCards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    // Force image visibility on page load
    function ensureImageVisibility() {
        // Find all game thumbnails
        const thumbnails = document.querySelectorAll('.game-thumbnail');
        
        thumbnails.forEach(function(img) {
            // Force visibility
            img.style.display = 'block';
            img.style.opacity = '1';
            img.style.visibility = 'visible';
            
            // Handle loading states
            if (img.complete) {
                img.style.opacity = '1';
            } else {
                img.addEventListener('load', function() {
                    this.style.display = 'block';
                    this.style.opacity = '1';
                    this.style.visibility = 'visible';
                });
                
                img.addEventListener('error', function() {
                    // Set placeholder if image fails to load
                    this.src = this.src.includes('placeholder') ? this.src : 
                        (typeof lofygame_theme_url !== 'undefined' ? 
                            lofygame_theme_url + '/images/placeholder-game.jpg' : 
                            '/wp-content/themes/lofygame-template/images/placeholder-game.jpg');
                    this.style.display = 'block';
                    this.style.opacity = '1';
                });
            }
        });
        
        // Hide all video previews by default
        const videos = document.querySelectorAll('.game-preview-video');
        videos.forEach(function(video) {
            video.style.display = 'none';
            video.style.opacity = '0';
            video.classList.remove('playing');
        });
    }
    
    // Enhanced minimal video preview - FIXED VERSION
    function initializeMinimalVideoPreview() {
        const gameCards = document.querySelectorAll('.game-card.minimal-card[data-has-video="true"]');
        
        gameCards.forEach(function(card) {
            const cardImage = card.querySelector('.minimal-image');
            const video = card.querySelector('.game-preview-video');
            const thumbnail = card.querySelector('.game-thumbnail');
            
            if (!cardImage || !video || !thumbnail) return;
            
            let hoverTimeout;
            let isVideoLoaded = false;
            let isPlaying = false;
            
            // CRITICAL: Ensure thumbnail is always visible
            thumbnail.style.display = 'block';
            thumbnail.style.opacity = '1';
            thumbnail.style.visibility = 'visible';
            
            // CRITICAL: Ensure video is hidden by default
            video.style.display = 'none';
            video.style.opacity = '0';
            video.classList.remove('playing');
            
            // Preload video on first hover
            function preloadVideo() {
                if (!isVideoLoaded && video.dataset.src) {
                    cardImage.classList.add('loading-video');
                    
                    // Set video source if not already set
                    if (!video.src) {
                        video.src = video.dataset.src;
                        video.load();
                    }
                    
                    video.addEventListener('loadeddata', function() {
                        isVideoLoaded = true;
                        cardImage.classList.remove('loading-video');
                    }, { once: true });
                    
                    video.addEventListener('error', function() {
                        cardImage.classList.remove('loading-video');
                        cardImage.classList.add('video-error');
                        console.error('Video failed to load:', video.dataset.src);
                    }, { once: true });
                }
            }
            
            // Play video on hover - FIXED VERSION
            function playVideo() {
                if (!isVideoLoaded || isPlaying) return;
                
                // Show video
                video.style.display = 'block';
                video.style.opacity = '1';
                video.currentTime = 0;
                
                const playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.then(function() {
                        isPlaying = true;
                        video.classList.add('playing');
                        
                        // FIXED: Don't hide thumbnail completely, just dim it
                        thumbnail.style.opacity = '0.3';
                        thumbnail.style.display = 'block'; // Keep it visible
                    }).catch(function(error) {
                        console.error('Video play failed:', error);
                        // Reset to thumbnail on error
                        pauseVideo();
                    });
                }
            }
            
            // Pause video and restore thumbnail - FIXED VERSION
            function pauseVideo() {
                if (!video) return;
                
                // Stop video
                if (!video.paused) {
                    video.pause();
                }
                
                // Hide video
                video.style.display = 'none';
                video.style.opacity = '0';
                video.classList.remove('playing');
                isPlaying = false;
                
                // CRITICAL: Always restore thumbnail visibility
                thumbnail.style.opacity = '1';
                thumbnail.style.display = 'block';
                thumbnail.style.visibility = 'visible';
            }
            
            // Mouse enter event
            card.addEventListener('mouseenter', function() {
                // Ensure thumbnail is visible first
                thumbnail.style.display = 'block';
                thumbnail.style.opacity = '1';
                
                // Preload video if not loaded
                if (!isVideoLoaded) {
                    preloadVideo();
                }
                
                // Clear any existing timeout
                clearTimeout(hoverTimeout);
                
                // Delay video start for better UX
                hoverTimeout = setTimeout(function() {
                    if (card.matches(':hover') && isVideoLoaded) {
                        playVideo();
                    }
                }, 300); // Slightly longer delay
            });
            
            // Mouse leave event
            card.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimeout);
                pauseVideo();
            });
            
            // Handle video end - loop if still hovering
            video.addEventListener('ended', function() {
                if (card.matches(':hover')) {
                    video.currentTime = 0;
                    video.play();
                } else {
                    pauseVideo();
                }
            });
            
            // Handle video errors
            video.addEventListener('error', function() {
                pauseVideo();
                cardImage.classList.add('video-error');
            });
            
            // Force thumbnail visibility on load
            if (thumbnail.complete) {
                thumbnail.style.display = 'block';
                thumbnail.style.opacity = '1';
            } else {
                thumbnail.addEventListener('load', function() {
                    this.style.display = 'block';
                    this.style.opacity = '1';
                });
            }
        });
    }
    
    // Enhanced mobile handling - FIXED VERSION
    function setupMinimalMobileHandling() {
        const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            const gameCards = document.querySelectorAll('.game-card.minimal-card[data-has-video="true"]');
            
            gameCards.forEach(function(card) {
                const cardImage = card.querySelector('.minimal-image');
                const video = card.querySelector('.game-preview-video');
                const thumbnail = card.querySelector('.game-thumbnail');
                
                if (!cardImage || !video || !thumbnail) return;
                
                // Ensure thumbnail is always visible on mobile
                thumbnail.style.display = 'block';
                thumbnail.style.opacity = '1';
                
                // On mobile, toggle video on tap
                cardImage.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    
                    if (video && video.dataset.src) {
                        if (!video.src) {
                            video.src = video.dataset.src;
                            video.load();
                        }
                        
                        if (video.paused) {
                            video.style.display = 'block';
                            video.style.opacity = '1';
                            video.play().then(function() {
                                video.classList.add('playing');
                                thumbnail.style.opacity = '0.3'; // Don't hide completely
                            }).catch(function() {
                                // Reset on error
                                video.style.display = 'none';
                                thumbnail.style.opacity = '1';
                            });
                        } else {
                            video.pause();
                            video.style.display = 'none';
                            video.style.opacity = '0';
                            video.classList.remove('playing');
                            thumbnail.style.opacity = '1';
                        }
                    }
                }, { passive: false });
            });
        }
    }
    
    // Lazy load videos for better performance
    function setupMinimalVideoLazyLoading() {
        if ('IntersectionObserver' in window) {
            const videoObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const card = entry.target;
                        const thumbnail = card.querySelector('.game-thumbnail');
                        
                        // Ensure thumbnail is visible when card comes into view
                        if (thumbnail) {
                            thumbnail.style.display = 'block';
                            thumbnail.style.opacity = '1';
                            thumbnail.style.visibility = 'visible';
                        }
                        
                        videoObserver.unobserve(card);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });
            
            const videoCards = document.querySelectorAll('.game-card.minimal-card');
            videoCards.forEach(function(card) {
                videoObserver.observe(card);
            });
        }
    }
    
    // Enhanced visibility handling
    function setupMinimalVisibilityHandling() {
        // When page becomes hidden, pause all videos and restore thumbnails
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                const playingVideos = document.querySelectorAll('.game-preview-video.playing');
                playingVideos.forEach(function(video) {
                    video.pause();
                    video.style.display = 'none';
                    video.style.opacity = '0';
                    video.classList.remove('playing');
                    
                    // Restore thumbnail
                    const card = video.closest('.game-card');
                    if (card) {
                        const thumbnail = card.querySelector('.game-thumbnail');
                        if (thumbnail) {
                            thumbnail.style.display = 'block';
                            thumbnail.style.opacity = '1';
                            thumbnail.style.visibility = 'visible';
                        }
                    }
                });
            }
        });
        
        // Handle page focus/blur
        window.addEventListener('blur', function() {
            ensureImageVisibility();
        });
        
        window.addEventListener('focus', function() {
            ensureImageVisibility();
        });
    }
    
    // Grid layout optimization
    function optimizeGridLayout() {
        const gameCards = document.querySelectorAll('.games-grid-12-col .game-card.minimal-card');
        
        gameCards.forEach(function(card, index) {
            // Add slight delay to card animations for better visual effect
            card.style.animationDelay = (index * 0.05) + 's';
            
            // Ensure proper aspect ratio
            const cardLink = card.querySelector('.game-card-link');
            if (cardLink) {
                cardLink.style.aspectRatio = '1 / 1';
            }
        });
    }
    
    // Enhanced performance optimization
    function setupPerformanceOptimizations() {
        // Throttle scroll events
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            
            scrollTimeout = setTimeout(function() {
                // Pause videos not in viewport and ensure thumbnails are visible
                const gameCards = document.querySelectorAll('.game-card.minimal-card[data-has-video="true"]');
                gameCards.forEach(function(card) {
                    const rect = card.getBoundingClientRect();
                    const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                    const thumbnail = card.querySelector('.game-thumbnail');
                    const video = card.querySelector('.game-preview-video');
                    
                    if (!isVisible) {
                        // Pause video if not visible
                        if (video && !video.paused) {
                            video.pause();
                            video.style.display = 'none';
                            video.style.opacity = '0';
                            video.classList.remove('playing');
                        }
                        
                        // Ensure thumbnail is visible
                        if (thumbnail) {
                            thumbnail.style.display = 'block';
                            thumbnail.style.opacity = '1';
                            thumbnail.style.visibility = 'visible';
                        }
                    } else {
                        // Ensure thumbnail is visible for visible cards
                        if (thumbnail) {
                            thumbnail.style.display = 'block';
                            thumbnail.style.opacity = '1';
                            thumbnail.style.visibility = 'visible';
                        }
                    }
                });
            }, 100);
        });
        
        // Use passive event listeners for better performance
        const gameCards = document.querySelectorAll('.game-card');
        gameCards.forEach(function(card) {
            card.addEventListener('touchstart', function() {}, { passive: true });
            card.addEventListener('touchmove', function() {}, { passive: true });
        });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // CRITICAL: Ensure images are visible first
        ensureImageVisibility();
        
        // Then initialize other features
        initializeGameCards();
        initializeMinimalVideoPreview();
        setupMinimalMobileHandling();
        setupMinimalVideoLazyLoading();
        setupMinimalVisibilityHandling();
        initializePagination();
        optimizeGridLayout();
        setupPerformanceOptimizations();
        
        // Force image visibility after a short delay to ensure DOM is ready
        setTimeout(ensureImageVisibility, 100);
        
        // Initialize fullscreen detection
        if (gameIframe) {
            // Add fullscreen attribute to iframe
            gameIframe.setAttribute('allowfullscreen', 'true');
            gameIframe.setAttribute('webkitallowfullscreen', 'true');
            gameIframe.setAttribute('mozallowfullscreen', 'true');
        }
    });
    
    // Lazy loading for iframes
    const gameIframes = document.querySelectorAll('.game-iframe');
    
    if ('IntersectionObserver' in window) {
        const iframeObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    if (iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        iframe.removeAttribute('data-src');
                    }
                    iframeObserver.unobserve(iframe);
                }
            });
        });
        
        gameIframes.forEach(function(iframe) {
            iframeObserver.observe(iframe);
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Mobile menu toggle (if needed)
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const headerMenu = document.querySelector('.header-menu');
    
    if (mobileMenuToggle && headerMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            headerMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // Game view tracking (for SEO and analytics)
    if (document.body.classList.contains('single-game')) {
        const gameId = document.body.getAttribute('data-game-id') || $('body').data('game-id');
        if (gameId) {
            // Track game view
            $.ajax({
                url: lofygame_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'track_game_view',
                    game_id: gameId,
                    nonce: lofygame_ajax.nonce
                }
            });
        }
    }
    
    // Share functionality
    const shareButtons = document.querySelectorAll('.share-button');
    
    shareButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            const platform = this.getAttribute('data-platform');
            
            let shareUrl = '';
            
            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                    break;
                case 'pinterest':
                    shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&description=${encodeURIComponent(title)}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, 'share', 'width=600,height=400');
            }
        });
    });

    // Enhanced walkthrough functionality
    document.addEventListener('DOMContentLoaded', function() {
        
        // Walkthrough button click tracking
        const walkthroughButton = document.querySelector('.walkthrough-button');
        
        if (walkthroughButton) {
            walkthroughButton.addEventListener('click', function(e) {
                // Add visual feedback
                this.classList.add('loading');
                
                // Track walkthrough click
                const gameId = document.body.getAttribute('data-game-id') || document.querySelector('.single-game-container').getAttribute('data-game-id');
                
                if (gameId && typeof lofygame_ajax !== 'undefined') {
                    // Track walkthrough access
                    jQuery.ajax({
                        url: lofygame_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'track_walkthrough_access',
                            game_id: gameId,
                            nonce: lofygame_ajax.nonce
                        },
                        success: function(response) {
                            console.log('Walkthrough access tracked');
                        }
                    });
                }
                
                // Visual feedback - change to success state after delay
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.classList.add('clicked');
                    
                    // Reset state after 3 seconds
                    setTimeout(() => {
                        this.classList.remove('clicked');
                    }, 3000);
                }, 1000);
            });
        }
    });

    // Re-initialize when new content is loaded (for AJAX)
    document.addEventListener('gamesLoaded', function() {
        ensureImageVisibility(); // CRITICAL: Always ensure images are visible first
        initializeMinimalVideoPreview();
        initializeGameCards();
        optimizeGridLayout();
        setupPerformanceOptimizations();
    });
    
    // Enhanced utility functions
    window.LofyGameMinimalVideo = {
        // Stop all video previews and restore thumbnails
        stopAllPreviews: function() {
            const videos = document.querySelectorAll('.game-preview-video');
            videos.forEach(function(video) {
                video.pause();
                video.style.display = 'none';
                video.style.opacity = '0';
                video.classList.remove('playing');
            });
            
            // Restore all thumbnails
            const thumbnails = document.querySelectorAll('.game-thumbnail');
            thumbnails.forEach(function(thumbnail) {
                thumbnail.style.display = 'block';
                thumbnail.style.opacity = '1';
                thumbnail.style.visibility = 'visible';
            });
        },
        
        // Play specific card video
        playCardVideo: function(cardElement) {
            const video = cardElement.querySelector('.game-preview-video');
            const thumbnail = cardElement.querySelector('.game-thumbnail');
            
            if (video && video.dataset.src) {
                if (!video.src) {
                    video.src = video.dataset.src;
                    video.load();
                }
                video.style.display = 'block';
                video.style.opacity = '1';
                video.play().then(function() {
                    video.classList.add('playing');
                    if (thumbnail) {
                        thumbnail.style.opacity = '0.3'; // Don't hide completely
                    }
                });
            }
        },
        
        // Reinitialize grid after content changes
        reinitializeGrid: function() {
            ensureImageVisibility();
            initializeGameCards();
            initializeMinimalVideoPreview();
            optimizeGridLayout();
        },
        
        // Force all images to be visible
        forceImageVisibility: function() {
            ensureImageVisibility();
        }
    };
    
    // Global pagination handling
    window.LofyGamePagination = {
        loadPage: function(page) {
            if (typeof loadPage === 'function') {
                loadPage(page);
            }
        },
        
        getCurrentPage: function() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('paged') || 1;
        },
        
        updateURL: function(page) {
            const url = new URL(window.location);
            url.searchParams.set('paged', page);
            if (history.pushState) {
                history.pushState(null, null, url.toString());
            }
        }
    };
    
    // CSS animation support check
    function checkAnimationSupport() {
        const hasAnimationSupport = 
            typeof document.body.style.animationName !== 'undefined' ||
            typeof document.body.style.webkitAnimationName !== 'undefined';
        
        if (!hasAnimationSupport) {
            // Fallback for browsers without animation support
            document.body.classList.add('no-animations');
        }
    }
    
    // Debug function to check image visibility
    function debugImageVisibility() {
        const thumbnails = document.querySelectorAll('.game-thumbnail');
        console.log('Total thumbnails found:', thumbnails.length);
        
        thumbnails.forEach(function(img, index) {
            const computed = window.getComputedStyle(img);
            console.log(`Thumbnail ${index}:`, {
                display: computed.display,
                opacity: computed.opacity,
                visibility: computed.visibility,
                src: img.src,
                naturalWidth: img.naturalWidth,
                naturalHeight: img.naturalHeight
            });
        });
    }
    
    // Make debug function available globally
    window.debugImageVisibility = debugImageVisibility;
    
    // Handle window load to ensure all images are processed
    window.addEventListener('load', function() {
        setTimeout(function() {
            ensureImageVisibility();
            console.log('Images visibility ensured after window load');
        }, 200);
    });
    
    // Handle mutations to the DOM (for dynamically loaded content)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            let shouldUpdate = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.classList && node.classList.contains('game-card') ||
                                node.querySelector && node.querySelector('.game-card')) {
                                shouldUpdate = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldUpdate) {
                setTimeout(function() {
                    ensureImageVisibility();
                    initializeMinimalVideoPreview();
                }, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Additional safeguards for image loading
    document.addEventListener('DOMContentLoaded', function() {
        // Set up image error handling
        document.addEventListener('error', function(e) {
            if (e.target.tagName === 'IMG' && e.target.classList.contains('game-thumbnail')) {
                console.log('Image failed to load:', e.target.src);
                
                // Try to set a placeholder
                if (!e.target.src.includes('placeholder')) {
                    e.target.src = e.target.getAttribute('data-placeholder') || 
                        '/wp-content/themes/lofygame-template/images/placeholder-game.jpg';
                }
                
                // Ensure it's still visible
                e.target.style.display = 'block';
                e.target.style.opacity = '1';
                e.target.style.visibility = 'visible';
            }
        }, true);
        
        // Handle successful image loads
        document.addEventListener('load', function(e) {
            if (e.target.tagName === 'IMG' && e.target.classList.contains('game-thumbnail')) {
                e.target.style.display = 'block';
                e.target.style.opacity = '1';
                e.target.style.visibility = 'visible';
            }
        }, true);
    });
    
    // CSS injection to ensure images are always visible (as backup)
    const criticalCSS = `
    .game-thumbnail {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .minimal-image img {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .game-preview-video:not(.playing) {
        display: none !important;
        opacity: 0 !important;
    }
    `;
    
    // Inject critical CSS
    const style = document.createElement('style');
    style.textContent = criticalCSS;
    document.head.appendChild(style);
    
    // Check animation support
    checkAnimationSupport();
    
    console.log('LofyGame theme loaded - Image visibility fixes applied');
    



    
/**
 * Enhanced Rating System JavaScript
 * Add this to your theme.js file or create a separate ratings.js file
 */

// AJAX handler for rating submission
function lofygame_handle_rating_submission() {
    // Add AJAX action for rating submission
    jQuery(document).ready(function($) {
        // Handle rating submission
        $(document).on('click', '#submit-rating', function(e) {
            e.preventDefault();
            
            const gameId = $(this).closest('[data-game-id]').data('game-id');
            const selectedRating = $('.rating-star-input.highlighted').length;
            
            if (selectedRating > 0 && gameId) {
                const button = $(this);
                const feedback = $('#rating-feedback');
                
                // Disable button and show loading
                button.prop('disabled', true).text('Submitting...');
                
                // Send AJAX request
                $.ajax({
                    url: lofygame_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'submit_game_rating',
                        game_id: gameId,
                        rating: selectedRating,
                        nonce: lofygame_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text('Rating Submitted!').css('background', '#28a745');
                            feedback.text('Thank you for your rating!').css('color', '#28a745').show();
                            
                            // Update page rating if new average is returned
                            if (response.data.new_average) {
                                updatePageRating(response.data.new_average, response.data.new_count);
                            }
                            
                            // Disable rating input
                            $('.rating-star-input').css('pointer-events', 'none');
                            
                        } else {
                            button.prop('disabled', false).text('Submit Rating');
                            feedback.text(response.data || 'Error submitting rating').css('color', '#dc3545').show();
                        }
                    },
                    error: function() {
                        button.prop('disabled', false).text('Submit Rating');
                        feedback.text('Network error. Please try again.').css('color', '#dc3545').show();
                    }
                });
            }
        });
        
        // Interactive rating stars
        $(document).on('mouseenter', '.rating-star-input', function() {
            const rating = $(this).data('rating');
            highlightRatingStars(rating);
        });
        
        $(document).on('mouseleave', '.rating-input', function() {
            const selectedRating = $('.rating-star-input.active').length;
            highlightRatingStars(selectedRating);
        });
        
        $(document).on('click', '.rating-star-input', function() {
            const rating = $(this).data('rating');
            
            // Remove active class from all stars
            $('.rating-star-input').removeClass('active');
            
            // Add active class to selected stars
            for (let i = 1; i <= rating; i++) {
                $(`.rating-star-input[data-rating="${i}"]`).addClass('active');
            }
            
            // Enable submit button
            $('#submit-rating').prop('disabled', false);
            
            // Show feedback
            $('#rating-feedback').text(`You selected ${rating} star${rating !== 1 ? 's' : ''}`).css('color', '#666').show();
        });
    });
    
    function highlightRatingStars(rating) {
        $('.rating-star-input').removeClass('highlighted');
        for (let i = 1; i <= rating; i++) {
            $(`.rating-star-input[data-rating="${i}"]`).addClass('highlighted');
        }
    }
    
    function updatePageRating(newAverage, newCount) {
        // Update all rating displays on the page
        $('.rating-score-header, .rating-score-mini, .rating-number-large').text(newAverage);
        $('.rating-count-header, .rating-count-mini, .rating-count-large').text(`(${newCount.toLocaleString()} reviews)`);
        
        // Update star displays
        updateStarDisplays(newAverage);
    }
    
    function updateStarDisplays(rating) {
        const starContainers = [
            '.rating-stars-header',
            '.rating-stars-mini-bar', 
            '.rating-stars-display'
        ];
        
        starContainers.forEach(container => {
            $(container).find('.star, .star-header, .star-mini-bar').each(function(index) {
                const starPosition = index + 1;
                $(this).removeClass('filled half empty');
                
                if (starPosition <= Math.floor(rating)) {
                    $(this).addClass('filled');
                } else if (starPosition - 0.5 <= rating) {
                    $(this).addClass('half');
                } else {
                    $(this).addClass('empty');
                }
            });
        });
    }
}

// Enhanced game card rating animations
function lofygame_enhance_rating_animations() {
    jQuery(document).ready(function($) {
        // Animate rating badges on hover
        $('.game-card').on('mouseenter', function() {
            const ratingBadge = $(this).find('.game-rating-badge');
            const rating = parseFloat($(this).data('rating')) || 0;
            
            // Add pulsing effect for high-rated games
            if (rating >= 4.5) {
                ratingBadge.addClass('excellent-rating');
            } else if (rating >= 4.0) {
                ratingBadge.addClass('good-rating');
            }
        });
        
        $('.game-card').on('mouseleave', function() {
            $(this).find('.game-rating-badge').removeClass('excellent-rating good-rating');
        });
        
        // Animate stars in sequence on page load
        $('.rating-stars-large .star-large').each(function(index) {
            $(this).css('animation-delay', (index * 0.2) + 's');
        });
        
        // Rating bar animation
        $('.rating-fill').each(function() {
            const width = $(this).css('width');
            $(this).css('width', '0').animate({ width: width }, 1000);
        });
    });
}

// Rating tooltips and accessibility
function lofygame_add_rating_accessibility() {
    jQuery(document).ready(function($) {
        // Add tooltips to rating stars
        $('.star, .star-mini, .star-large').each(function() {
            const container = $(this).closest('[data-rating]');
            const rating = container.data('rating');
            
            if (rating) {
                $(this).attr('title', `Rated ${rating} out of 5 stars`);
            }
        });
        
        // Add ARIA labels
        $('.rating-star-input').each(function() {
            const rating = $(this).data('rating');
            $(this).attr('aria-label', `Rate ${rating} star${rating !== 1 ? 's' : ''}`);
        });
        
        // Keyboard navigation for rating input
        $('.rating-star-input').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });
        
        // Add tabindex for keyboard navigation
        $('.rating-star-input').attr('tabindex', '0');
    });
}

// Rich snippets validation helper
function lofygame_validate_rich_snippets() {
    if (typeof console !== 'undefined' && window.location.hostname !== 'localhost') {
        const structuredData = $('script[type="application/ld+json"]');
        
        if (structuredData.length > 0) {
            try {
                const data = JSON.parse(structuredData.first().text());
                
                if (data.aggregateRating) {
                    console.log('✅ Rich Snippets: AggregateRating found', data.aggregateRating);
                    
                    // Validate rating values
                    const rating = parseFloat(data.aggregateRating.ratingValue);
                    const count = parseInt(data.aggregateRating.ratingCount);
                    
                    if (rating < 1 || rating > 5) {
                        console.warn('⚠️ Rich Snippets: Rating value should be between 1-5');
                    }
                    
                    if (count < 1) {
                        console.warn('⚠️ Rich Snippets: Rating count should be at least 1');
                    }
                    
                    console.log('🔍 Test your rich snippets:', 
                        `https://search.google.com/test/rich-results?url=${encodeURIComponent(window.location.href)}`);
                } else {
                    console.warn('⚠️ Rich Snippets: No aggregateRating found in structured data');
                }
            } catch (e) {
                console.error('❌ Rich Snippets: Invalid JSON-LD structured data');
            }
        } else {
            console.warn('⚠️ Rich Snippets: No structured data found');
        }
    }
}

// Rating system analytics
function lofygame_rating_analytics() {
    jQuery(document).ready(function($) {
        // Track rating interactions
        $('.rating-star-input').on('click', function() {
            const rating = $(this).data('rating');
            const gameId = $(this).closest('[data-game-id]').data('game-id');
            
            // Send analytics event (customize for your analytics platform)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'rating_interaction', {
                    event_category: 'Game Rating',
                    event_label: 'Star Click',
                    value: rating,
                    custom_parameters: {
                        game_id: gameId
                    }
                });
            }
            
            // Track with Facebook Pixel if available
            if (typeof fbq !== 'undefined') {
                fbq('trackCustom', 'GameRating', {
                    rating: rating,
                    game_id: gameId
                });
            }
        });
        
        // Track rating submission
        $(document).on('ajax_rating_success', function(e, data) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'rating_submitted', {
                    event_category: 'Game Rating',
                    event_label: 'Rating Submitted',
                    value: data.rating
                });
            }
        });
    });
}

// Local storage for user ratings
function lofygame_rating_storage() {
    const STORAGE_KEY = 'lofygame_user_ratings';
    
    return {
        // Check if user has already rated this game
        hasRated: function(gameId) {
            const ratings = this.getUserRatings();
            return ratings.hasOwnProperty(gameId);
        },
        
        // Save user's rating
        saveRating: function(gameId, rating) {
            const ratings = this.getUserRatings();
            ratings[gameId] = {
                rating: rating,
                timestamp: Date.now()
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(ratings));
        },
        
        // Get user's rating for a game
        getUserRating: function(gameId) {
            const ratings = this.getUserRatings();
            return ratings[gameId] || null;
        },
        
        // Get all user ratings
        getUserRatings: function() {
            try {
                return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            } catch (e) {
                return {};
            }
        },
        
        // Clear old ratings (older than 30 days)
        cleanupOldRatings: function() {
            const ratings = this.getUserRatings();
            const thirtyDaysAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
            
            Object.keys(ratings).forEach(gameId => {
                if (ratings[gameId].timestamp < thirtyDaysAgo) {
                    delete ratings[gameId];
                }
            });
            
            localStorage.setItem(STORAGE_KEY, JSON.stringify(ratings));
        }
    };
}

// Initialize rating system
function lofygame_init_rating_system() {
    const ratingStorage = lofygame_rating_storage();
    
    jQuery(document).ready(function($) {
        // Clean up old ratings on page load
        ratingStorage.cleanupOldRatings();
        
        // Check if user has already rated current game
        const gameId = $('[data-game-id]').data('game-id');
        if (gameId && ratingStorage.hasRated(gameId)) {
            const userRating = ratingStorage.getUserRating(gameId);
            
            // Show user's previous rating
            $('.rating-star-input').each(function(index) {
                if (index < userRating.rating) {
                    $(this).addClass('active');
                }
            });
            
            // Disable rating input
            $('.rating-star-input').css('pointer-events', 'none');
            $('#submit-rating').prop('disabled', true).text('Already Rated');
            $('#rating-feedback').text(`You rated this game ${userRating.rating} stars`).css('color', '#28a745').show();
        }
        
        // Save rating when submitted
        $(document).on('ajax_rating_success', function(e, data) {
            if (data.game_id && data.rating) {
                ratingStorage.saveRating(data.game_id, data.rating);
            }
        });
    });
}

// Enhanced rating display for game cards
function lofygame_enhance_game_card_ratings() {
    jQuery(document).ready(function($) {
        // Add rating-based styling to game cards
        $('.game-card[data-rating]').each(function() {
            const rating = parseFloat($(this).data('rating'));
            const card = $(this);
            
            // Add rating class for styling
            if (rating >= 4.5) {
                card.addClass('rating-excellent');
            } else if (rating >= 4.0) {
                card.addClass('rating-very-good');
            } else if (rating >= 3.5) {
                card.addClass('rating-good');
            } else if (rating >= 3.0) {
                card.addClass('rating-average');
            } else {
                card.addClass('rating-below-average');
            }
            
            // Add subtle glow effect for highly rated games
            if (rating >= 4.5) {
                card.on('mouseenter', function() {
                    $(this).addClass('rating-glow-effect');
                }).on('mouseleave', function() {
                    $(this).removeClass('rating-glow-effect');
                });
            }
        });
        
        // Animate rating badges on scroll
        if (typeof IntersectionObserver !== 'undefined') {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const badge = entry.target;
                        badge.classList.add('rating-badge-animate');
                        observer.unobserve(badge);
                    }
                });
            }, { threshold: 0.1 });
            
            $('.game-rating-badge').each(function() {
                observer.observe(this);
            });
        }
    });
}

// Rating comparison and sorting
function lofygame_rating_comparison() {
    jQuery(document).ready(function($) {
        // Add rating comparison tooltips
        $('.game-card[data-rating]').each(function() {
            const rating = parseFloat($(this).data('rating'));
            const gameTitle = $(this).find('.minimal-game-title').text();
            
            let comparison = '';
            if (rating >= 4.5) {
                comparison = 'Excellent - Highly recommended!';
            } else if (rating >= 4.0) {
                comparison = 'Very Good - Great gameplay!';
            } else if (rating >= 3.5) {
                comparison = 'Good - Worth playing!';
            } else if (rating >= 3.0) {
                comparison = 'Average - Mixed reviews';
            } else {
                comparison = 'Below Average - Consider other options';
            }
            
            $(this).attr('title', `${gameTitle} - ${rating}/5 stars - ${comparison}`);
        });
    });
}

// Rating system performance optimization
function lofygame_optimize_rating_performance() {
    // Debounce rating animations
    let animationTimeout;
    
    function debounceRatingAnimation(callback, delay) {
        clearTimeout(animationTimeout);
        animationTimeout = setTimeout(callback, delay);
    }
    
    // Optimize star hover effects
    jQuery(document).ready(function($) {
        $('.rating-star-input').on('mouseenter', function() {
            const self = this;
            debounceRatingAnimation(function() {
                const rating = $(self).data('rating');
                highlightRatingStars(rating);
            }, 50);
        });
        
        // Lazy load rating animations
        $('.rating-stars-large').each(function() {
            const stars = $(this).find('.star-large');
            stars.css('opacity', '0');
            
            setTimeout(() => {
                stars.each(function(index) {
                    const star = $(this);
                    setTimeout(() => {
                        star.css({
                            opacity: '1',
                            transform: 'scale(1.1)'
                        }).animate({
                            transform: 'scale(1)'
                        }, 200);
                    }, index * 100);
                });
            }, 500);
        });
    });
}

// Initialize all rating system components
document.addEventListener('DOMContentLoaded', function() {
    // Core functionality
    lofygame_handle_rating_submission();
    lofygame_init_rating_system();
    
    // Enhancements
    lofygame_enhance_rating_animations();
    lofygame_add_rating_accessibility();
    lofygame_enhance_game_card_ratings();
    lofygame_rating_comparison();
    
    // Analytics and validation
    lofygame_rating_analytics();
    lofygame_validate_rich_snippets();
    
    // Performance optimization
    lofygame_optimize_rating_performance();
    
    console.log('✅ LofyGame Rating System Initialized');
});

// Global rating utility functions
window.LofyGameRating = {
    // Update a game's rating display
    updateGameRating: function(gameId, newRating, newCount) {
        const gameCards = document.querySelectorAll(`[data-game-id="${gameId}"]`);
        gameCards.forEach(card => {
            card.setAttribute('data-rating', newRating);
            
            // Update rating badges
            const ratingValue = card.querySelector('.rating-value-mini');
            if (ratingValue) ratingValue.textContent = newRating;
            
            // Update stars
            const stars = card.querySelectorAll('.star-mini');
            stars.forEach((star, index) => {
                star.className = 'star-mini';
                if (index < Math.floor(newRating)) {
                    star.classList.add('filled');
                } else if (index < newRating) {
                    star.classList.add('half');
                } else {
                    star.classList.add('empty');
                }
            });
        });
    },
    
    // Get average rating for games on page
    getPageAverageRating: function() {
        const ratings = [];
        document.querySelectorAll('[data-rating]').forEach(element => {
            const rating = parseFloat(element.getAttribute('data-rating'));
            if (!isNaN(rating)) ratings.push(rating);
        });
        
        if (ratings.length === 0) return 0;
        return ratings.reduce((sum, rating) => sum + rating, 0) / ratings.length;
    },
    
    // Show rating distribution
    showRatingDistribution: function() {
        const ratings = {};
        document.querySelectorAll('[data-rating]').forEach(element => {
            const rating = Math.floor(parseFloat(element.getAttribute('data-rating')));
            ratings[rating] = (ratings[rating] || 0) + 1;
        });
        
        console.log('Rating Distribution:', ratings);
        return ratings;
    },
    
    // Filter games by rating
    filterGamesByRating: function(minRating) {
        document.querySelectorAll('.game-card[data-rating]').forEach(card => {
            const rating = parseFloat(card.getAttribute('data-rating'));
            if (rating >= minRating) {
                card.style.display = '';
                card.classList.add('rating-filtered-visible');
            } else {
                card.style.display = 'none';
                card.classList.remove('rating-filtered-visible');
            }
        });
    },
    
    // Reset rating filter
    resetRatingFilter: function() {
        document.querySelectorAll('.game-card[data-rating]').forEach(card => {
            card.style.display = '';
            card.classList.remove('rating-filtered-visible');
        });
    }
};

// Add CSS classes for rating-based styling
const ratingCSS = `
<style>
.rating-excellent { 
    position: relative; 
}
.rating-excellent::after {
    content: '🏆';
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 10;
    font-size: 0.8rem;
}

.rating-glow-effect {
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.5) !important;
}

.rating-badge-animate {
    animation: ratingBadgeEntry 0.6s ease-out;
}

@keyframes ratingBadgeEntry {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.excellent-rating {
    animation: excellentPulse 2s infinite;
}

.good-rating {
    animation: goodPulse 3s infinite;
}

@keyframes excellentPulse {
    0%, 100% { 
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); 
    }
    50% { 
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.6); 
    }
}

@keyframes goodPulse {
    0%, 100% { 
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); 
    }
    50% { 
        box-shadow: 0 4px 16px rgba(0, 200, 0, 0.4); 
    }
}

/* Header rating styles */
.rating-stars-header {
    display: flex;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
}

.star-header {
    font-size: 1.5rem;
    color: #ddd;
    transition: all 0.3s ease;
}

.star-header.filled {
    color: #ffa500;
    text-shadow: 0 0 10px rgba(255, 165, 0, 0.5);
}

.rating-info-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rating-score-header {
    font-size: 1.8rem;
    font-weight: 700;
    color: #ffa500;
}

.rating-count-header {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.8);
}

/* Mini bar rating styles */
.rating-stars-mini-bar {
    display: flex;
    gap: 0.15rem;
}

.star-mini-bar {
    font-size: 0.9rem;
    color: #ddd;
}

.star-mini-bar.filled {
    color: #ffa500;
}

.rating-score-mini {
    font-weight: 700;
    color: #ffa500;
    margin-left: 0.5rem;
}

.rating-count-mini {
    font-size: 0.8rem;
    color: #666;
    margin-left: 0.25rem;
}
</style>
`;

// Inject the CSS
document.head.insertAdjacentHTML('beforeend', ratingCSS);
})(jQuery);