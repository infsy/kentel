
$(document).ready(function() {
	
		$(function(){
		  $('.form-horizontal select').selectric();
		});
	
		//div noir pour over
		$(".menu-premier-level").append("<div class='hover-btn'></div>");
		//on cache les sous-menus
		$(".menu-second-level").hide();
		//on insere les chevrons pour les liens des sous-menus
		$(".menu-second-level a").append(" <i class='fa fa-angle-right chevron-nav'></i>");
		//on insere le bouton close pour les sous-menus
		$(".menu-second-level").append("<button class='closeMenu'></button>");
		
		$(window).on("load resize" , function() {
			offset = $(".navbar-header").height()+$("footer").height();
			width = $(window).width();
			height = $(window).height();
			contentHeight = height-offset;
			
			$("#wrapper-content").css("min-height", (contentHeight-10) + "px");//on fixe une hauteur minimum à la zone de contenu pour que l'interface prenne tout l'écran
			$(".carre").css("width", ($(".espace-carre").width()-5) + "px");
			if(width>767){
				$(".menu-second-level").css("width",$("#navbar").innerWidth()-($(".navbar-header").innerWidth()) - 4);
				$(".menu-second-level").css("top",$(".menu-premier-level").height()+5);
			}else{
				$(".menu-second-level").css("width", "100%");
			}
			
			if(width<768){
				if($(".menu-second-level").css('opacity')==0){
					$(".menu-second-level").hide();
				}
			}
		});

		$(".btnNav").click(function(){
			width = $(window).width();
			
			//on verifie si le bouton n'est pas déja actif
			if($(this).hasClass("mobilActive")==false) {
				var leftStart = 50, leftEnd = 5;
				$(".triangle").remove();
				$(this).append("<div class='triangle'></div>");

				$(this).find(".triangle").css("opacity", "1");

				if($(this).closest(".navbar-nav").hasClass("navbar-right")){
					leftEnd = -$(this).parent().find(".menu-second-level").innerWidth() + $(this).parent().innerWidth();
					leftStart = leftEnd + 50;
				}

				$(".menu-premier-level").find(".hover-btn").removeClass("active-desktop");
				$(".menu-second-level").hide();//on cache tous les sous-menus
				$(".menu-second-level").css("left",leftStart+"px");
				$(".menu-second-level").css("top",$(".menu-premier-level").height()+5);
	
				$(this).parent().find(".menu-second-level").css("display","block");//on affiche le bon sous-menu
				$(this).parent().find(".menu-second-level").css("opacity","0");//on fixe l'opacité du sous-menu à 0 pour l'animer par la suite
				$(this).parent().find(".menu-second-level").animate({ opacity: 1, left:leftEnd }, 100);//on crée une animation pour le sous-menu
				
				$(this).find(".hover-btn").addClass("active-desktop");
				
				if (width>767){//En desktop on affiche le bouton close
					$(".closeNav").css("display", "block");
				}
				
				$(".menu-premier-level").removeClass("mobilActive");//on enleve la classe active sur tous les boutons
				$(this).addClass("mobilActive");//on active la classe active sur le bouton cliqué
				
			}else if (width<767){
				$(".menu-second-level").hide();
				$(".menu-premier-level").removeClass("mobilActive");//on enleve la classe active sur tous les boutons
			}
		});
		
		
		
		
		$("#wrapper-content").click(function() {//On cache les sous-menus en cliquant sur la zone de contenu
			width = $(window).width();
			if (width>767){
				$(".menu-second-level").animate({ opacity: 0, left:50 }, 100);
				setTimeout(function(){
						$(".menu-second-level").css("display","none");
					}, 200);
				$(".menu-premier-level").find(".hover-btn").removeClass("active-desktop");
				$(".triangle").css("opacity", "0");
				$(".menu-premier-level").removeClass("mobilActive");
			}
		});
		
		
		
		
		$(".closeMenu").click(function(){//on cache le sous-menu en cliquant sur le bouton close
			$(".menu-second-level").animate({ opacity: 0, left:50 }, 100);
				setTimeout(function(){
						$(".menu-second-level").css("display","none");
					}, 200);
				$(".menu-premier-level").find(".hover-btn").removeClass("active-desktop");
				$(".triangle").css("opacity", "0");
				$(".menu-premier-level").removeClass("mobilActive");
		});
		
		
		//Fonction pour centrer les fenetres modales
			
		(function ($) {
			"use strict";
			function centerModal() {
				$(this).css('display', 'block');
				var $dialog  = $(this).find(".modal-dialog"),
				offset       = ($(window).height() - $dialog.height()) / 2,
				bottomMargin = parseInt($dialog.css('marginBottom'), 10);
		
				if(offset < bottomMargin) offset = bottomMargin;
				$dialog.css("margin-top", offset);
			}
		
			$(document).on('show.bs.modal', '.modal', centerModal);
			$(window).on("resize", function () {
				$('.modal:visible').each(centerModal);
			});
		}(jQuery));

});//Fin du carré de sable Jquery
		
		
		
		
		
		