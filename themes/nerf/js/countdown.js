(function($){
	"use strict";
	$.fn.apusCountDown = function( options ) {
	 	return this.each(function() {
			new $.apusCountDown( this, options ); 
		});
 	}
	$.apusCountDown = function( obj, options ) {
		this.options = $.extend({
			autoStart : true,
			LeadingZero:true,
			DisplayFormat:"<div>%%D%% Days</div><div>%%H%% Hours</div><div>%%M%% Minutes</div><div>%%S%% Seconds</div>",
			FinishMessage:"Expired",
			CountActive:true,
			TargetDate:null
		}, options || {} );
		if ( this.options.TargetDate == null || this.options.TargetDate == '' ){
			return ;
		}
		this.timer  = null;
		this.element = obj;
		this.CountStepper = -1;
		this.CountStepper = Math.ceil(this.CountStepper);
		this.SetTimeOutPeriod = (Math.abs(this.CountStepper)-1)*1000 + 990;
		var dthen = new Date(this.options.TargetDate);
		var dnow = new Date();
		if ( this.CountStepper > 0 ) {
			var ddiff = new Date(dnow-dthen);
		} else {
			var ddiff = new Date(dthen-dnow);
		}
		var gsecs = Math.floor(ddiff.valueOf()/1000); 
		this.CountBack(gsecs, this);
	};
	$.apusCountDown.fn = $.apusCountDown.prototype;
    $.apusCountDown.fn.extend = $.apusCountDown.extend = $.extend;
	$.apusCountDown.fn.extend({
		calculateDate:function( secs, num1, num2 ){
			var s = ((Math.floor(secs/num1))%num2).toString();
			if ( this.options.LeadingZero && s.length < 2) {
				s = "0" + s;
			}
			return "<span>" + s + "</span>";
		},
		CountBack:function( secs, self ){
			if (secs < 0) {
				self.element.innerHTML = '<div class="labelexpired"> '+self.options.FinishMessage+"</div>";
				return;
			}
			clearInterval(self.timer);
			var DisplayStr = self.options.DisplayFormat.replace(/%%D%%/g, self.calculateDate( secs,86400,100000) );
			DisplayStr = DisplayStr.replace(/%%H%%/g, self.calculateDate(secs,3600,24));
			DisplayStr = DisplayStr.replace(/%%M%%/g, self.calculateDate(secs,60,60));
			DisplayStr = DisplayStr.replace(/%%S%%/g, self.calculateDate(secs,1,60));
			self.element.innerHTML = DisplayStr;
			if (self.options.CountActive) {
				self.timer = null;
				self.timer =  setTimeout( function(){
					self.CountBack((secs+self.CountStepper),self);
				},( self.SetTimeOutPeriod ) );
			}
		}
	});

	jQuery(document).ready(function($) {
		$('[data-time="timmer"]').each(function(index, el) {
            var $this = $(this);
            var $date = $this.data('date').split("-");
            var format = "<div class=\"times\"><div class=\"day\">%%D%% "+ "<span class=\"title\">" + nerf_countdown_opts.days + "</span>" +"</div><div class=\"hours\">%%H%% "+ "<span class=\"title\">" + nerf_countdown_opts.hours + "</span>" +"</div><div class=\"minutes\">%%M%% "+ "<span class=\"title\">" + nerf_countdown_opts.mins + "</span>" +"</div><div class=\"seconds\">%%S%% "+ "<span class=\"title\">" + nerf_countdown_opts.secs + "</span>" +"</div></div>";
            if ( $(this).data('format') ) {
            	format = $(this).data('format');
            }
            $this.apusCountDown({
                TargetDate:$date[0]+"/"+$date[1]+"/"+$date[2]+" "+$date[3]+":"+$date[4]+":"+$date[5],
                DisplayFormat: format,
                FinishMessage: "",
            });
        });
	});

	jQuery(window).on("elementor/frontend/init", function() {
    	// General element

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_countdown.default",
            function($scope) {

                $scope.find('[data-time="timmer-elementor"]').each(function(index, el) {
                	
		            var $this = $(this);
		            var $date = $this.data('date').split("-");
		            var format = "<div class=\"times\"><div class=\"day\">%%D%% "+ "<span class=\"title\">" + nerf_countdown_opts.days + "</span>" +"</div><div class=\"hours\">%%H%% "+ "<span class=\"title\">" + nerf_countdown_opts.hours + "</span>" +"</div><div class=\"minutes\">%%M%% "+ "<span class=\"title\">" + nerf_countdown_opts.mins + "</span>" +"</div><div class=\"seconds\">%%S%% "+ "<span class=\"title\">" + nerf_countdown_opts.secs + "</span>" +"</div></div>";
		            if ( $(this).data('format') ) {
		            	format = $(this).data('format');
		            }
		            $this.apusCountDown({
		                TargetDate:$date[0]+"/"+$date[1]+"/"+$date[2]+" "+$date[3]+":"+$date[4]+":"+$date[5],
		                DisplayFormat: format,
		                FinishMessage: "",
		            });
		        });
            }
        );
    });
})(jQuery)