$(function () {
    

    $('.aside-filter input').on('change', function() {
        $('.clear-btn').removeClass('hidden');
    });

    $('.clear-btn').on('click', function() {
        // Barcha input qiymatlarini tozalash
        $('.aside-filter input[type="checkbox"], .aside-filter input[type="radio"]').prop('checked', false);
        $('.aside-filter input[type="number"]').val('');

        $(this).addClass('hidden');
    });
    
    
    // header
    var lastScrollTop = 0;
    var header = $('.header');

    $(window).scroll(function () {
        var currentScroll = $(this).scrollTop();
        if (currentScroll > lastScrollTop) {
            // Scroll pastga bo'lganda
            header.css('top', '-100px');
        } else {
            // Scroll tepaga bo'lganda
            header.css('top', '0');
        }
        lastScrollTop = currentScroll;
    });


    // fixed nav
    $(function () {
        // Add the hover() method for #language-wrapper
        $('.fixed-nav').hover(
            function () {
                $('.hover-box').show();
            },
            function () {
                $('.hover-box').hide();
            }
        );
    });


    $(function() {
        var link = $('#navbar a.nav-line');
        
        // Move to specific section when clicking on a menu link
        link.on('click', function(e) {
            var target = $($(this).attr('href'));
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 600);
            
            link.removeClass('active');
            $('.fixed-nav-link').removeClass('active');
            
            $(this).addClass('active');
            $('.fixed-nav-link[href="' + $(this).attr('href') + '"]').addClass('active');
            
            e.preventDefault();
        });
        
        // Run the scrNav function on scroll
        $(window).on('scroll', function() {
            scrNav();
        });
        
        // scrNav function
        // Change active class according to the active section in the window
        function scrNav() {
            var sTop = $(window).scrollTop();
            $('.spy').each(function() {
                var id = $(this).attr('id'),
                    offset = $(this).offset().top - 1,
                    height = $(this).height();
                    
                if(sTop >= offset && sTop < offset + height) {
                    link.removeClass('active');
                    $('.fixed-nav-link').removeClass('active');
    
                    $('#navbar').find('[data-scroll="' + id + '"]').addClass('active');
                    $('.hover-block').find('a[href="#' + id + '"]').addClass('active');
                }
            });
        }
        scrNav();
    });
    
    


    // popup
    $(document).ready(function() {

        // Modalni ko'rsatish
        $('.table-img').hover(
            function() {
                $(this).find('.modal-body').stop(true, true).fadeIn();
            },
            function() {
                $(this).find('.modal-body').stop(true, true).fadeOut();
            }
        );
        
        // Modalni ichidagi tasvirga hover qilishda
        $('.modal-body').hover(
            function() {
                $(this).stop(true, true).fadeIn();
            },
            function() {
                $(this).stop(true, true).fadeOut();
            }
        );
        
        $('.thumbnail').hover(function() {
          // Katta rasm URL-sini data atributidan olish
          var largeImageURL = $(this).data('large');
          $('.image-viewer').attr('src', largeImageURL);
          
          // Popupni ko'rsatish va uni kursor nisbatan joylashtirish
          $('.image-popup').removeClass('hidden');
        }, function() {
          // Popupni yashirish, agar kursor thumbnaildan chiqib ketsa
          $('.image-popup').addClass('hidden');
        });
      
        $('.image-popup').hover(function() {
          // Kursor popup ustida bo'lsa, uni ko'rsatishni davom ettirish
          $(this).show();
        }, function() {
          // Popupni kursor chiqib ketgandan keyin yashirish
          $(this).addClass('hidden');
        });
    });


    var sidebar = new StickySidebar('.sidebar', {
        containerSelector: '.main-content',
        innerWrapperSelector: '.aside-filter',
        topSpacing: 20,
        bottomSpacing: 20
    });
    
});

