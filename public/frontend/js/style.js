$(function ($) {
    var menujs = function() {
        $(document.body).on('click','#icon-bar',function(){
            $('#menu-bar').addClass('d-block');
            $('.header-top').addClass('d-none')
        });
        $(document.body).on('click','.menu-cancel',function () {
            $('#menu-bar').removeClass('d-block');
            $('.header-top').removeClass('d-none')
        })
    };
    var menunavigation = function() {
        $(document).ready(function(){
            $(window).scroll(function() {
                if ($(window).scrollTop() > 40) {
                    $('header').addClass('scroll-fixed');
                    $('.anchorlink').css('scroll-margin-top', '80px')
                } else {
                    $('header').removeClass('scroll-fixed');
                    $('.anchorlink').css('scroll-margin-top', '200px')
                }
            });
        })
    };
    function resizeList(parent, screen) {
        var wwindow= $(window).width();
        if (wwindow > screen) {
            ($(parent).removeClass('parent-simple-list'));
        }
        if (wwindow < screen) {
            ($(parent).addClass('parent-simple-list'));
        }
    }
    var carrerList = function(){
        $(window).on('load resize', function() {
            resizeList(".js_list_parent_simple", 991);
        });
    };
    function resizeToogleList(parent, screen) {
        var wwindow= $(window).width();
        if (wwindow > screen) {
            ($(parent).addClass('show'));
        }
        if (wwindow < screen) {
            ($(parent).removeClass('show'));
        }
    }
    var carrerToogleList = function(){
        $(window).on('load resize', function() {
            resizeToogleList("#listCategoriesCarreer", 991);
        });
    };
    var gotoplayout = function() {
        $('.icon-gotop').on("click", function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('.icon-gotop').fadeIn();
            } else {
                $('.icon-gotop').fadeOut();
            }
        });
    };
    $(function () {
        menujs();
        menunavigation();
        carrerList();
        carrerToogleList();
        gotoplayout();
        $(".pt-popover").popover({
            trigger: 'hover',
            html: true
        });
    });
});
$(document).ready(function() {
    var API_SUGGEST = WEB_URL+'suggest';
    $(".searchsuggest").autocomplete({
        source : function( request, response ) {
            $.ajax({
                url : API_SUGGEST ,
                type: "GET",
                cache: false,
                dataType: "json",
                data: {'searchword': request.term},
                success: function( data ) {
                    response( data );
                }
            });
        }, minLength: 1
    });
});
function checkform() {
    var txtSearch = $('#txtSearch').val().trim();
    if(txtSearch.length < 1) {
        $('#txtSearch.searchsuggest').focus();
        $('.msg-keyword').text(txt_please_enter_keyword);
        $('input[name=keyword]').parent().addClass("has-error");
        return false;
    } else {
        $('.msg-keyword').text('');
        $('input[name=keyword]').parent().removeClass("has-error");
        $('#frmSearch').submit();
    }
}
function checkformheader() {
    var txtSearch = $('#txtSearchHeader').val().trim();
    if(txtSearch.length < 1) {
        $('#txtSearchHeader.searchsuggest').focus();
        return false;
    } else {
        $('#frmSearchHeader').submit();
    }
}