(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Game URL validation
        $('#game_url').on('blur', function() {
            var gameUrl = $(this).val();
            var $status = $('#game-url-status');
            
            if (!$status.length) {
                $(this).after('<div id="game-url-status" style="margin-top: 5px;"></div>');
                $status = $('#game-url-status');
            }
            
            if (gameUrl) {
                $status.html('<span style="color: #666;">⏳ Validating...</span>');
                
                $.ajax({
                    url: lofygame_admin_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'preview_game',
                        game_url: gameUrl,
                        nonce: lofygame_admin_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $status.html('<span style="color: #46b450;">✅ Game URL is valid</span>');
                        } else {
                            $status.html('<span style="color: #dc3232;">❌ ' + response.data + '</span>');
                        }
                    },
                    error: function() {
                        $status.html('<span style="color: #dc3232;">❌ Validation failed</span>');
                    }
                });
            } else {
                $status.empty();
            }
        });
        
        // Add preview button for game URL
        if ($('#game_url').length) {
            $('#game_url').after('<button type="button" id="preview-game" class="button" style="margin-left: 10px;">Preview Game</button>');
            
            $('#preview-game').on('click', function(e) {
                e.preventDefault();
                var gameUrl = $('#game_url').val();
                
                if (gameUrl) {
                    window.open(gameUrl, '_blank', 'width=800,height=600');
                } else {
                    alert('Please enter a game URL first.');
                }
            });
        }
        
        // Character counter for How to Play
        if ($('#how_to_play').length) {
            $('#how_to_play').after('<div id="how-to-play-counter" style="margin-top: 5px; color: #666; font-size: 12px;"></div>');
            
            function updateCounter() {
                var text = $('#how_to_play').val();
                var length = text.length;
                var words = text.trim() ? text.trim().split(/\s+/).length : 0;
                
                $('#how-to-play-counter').text(length + ' characters, ' + words + ' words');
                
                if (length > 500) {
                    $('#how-to-play-counter').css('color', '#dc3232');
                } else if (length > 300) {
                    $('#how-to-play-counter').css('color', '#ffb900');
                } else {
                    $('#how-to-play-counter').css('color', '#666');
                }
            }
            
            $('#how_to_play').on('input', updateCounter);
            updateCounter(); // Initial count
        }
    });
    
})(jQuery);