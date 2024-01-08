(function($){
	$.fn.CustomDate = function( options ) {
	 	return this.each(function() {
			new  $.CustomDate( this, options );
		});
 	 }
	$.CustomDate = function( obj, options ){

		this.options = $.extend({
				formatTag : '1',
				autoStart: false,
				leadingZero:true,
				displayFormat:"<div>%%D%% Days</div><div>%%H%% Hours</div><div>%%M%% Minutes</div><div>%%S%% Seconds</div>",
				deaLine:"Message",
				countActive:true,
				targetDate:null
		}, options || {} );
		if( this.options.targetDate == null || this.options.targetDate == '' ){
			return ;
		}
		this.timer  = null;
		this.element = obj;
		this.CountStepper = -1;
		this.CountStepper = Math.ceil(this.CountStepper);
		this.SetTimeOutPeriod = (Math.abs(this.CountStepper)-1)*1000 + 990;
		var dthen = new Date(this.options.targetDate);
		var dnow = new Date();
		if( this.CountStepper > 0 ) {
			ddiff = new Date(dnow-dthen);
		}
		else {
			 ddiff = new Date(dthen-dnow);
		}
		gsecs = Math.floor(ddiff.valueOf()/1000); 
		this.CountBack(gsecs, this);

	};
	 $.CustomDate.fn =  $.CustomDate.prototype;
     $.CustomDate.fn.extend =  $.CustomDate.extend = $.extend;
	 $.CustomDate.fn.extend({
		calculateDate:function( secs, num1, num2 ){
			  var s = ((Math.floor(secs/num1))%num2).toString();
			  if ( this.options.leadingZero && s.length < 2) {
					s = "0" + s;
			  }
			  if(this.options.formatTag == 1)
			  	return "<b>" + s + "</b>";
			  else if(this.options.formatTag == 2)
			  {
			  	var n = s.split("");
			  	if (typeof(n[2])=="undefined")
					return "<div class=\"countdown_num\"><span>" + n[0] + "</span><span>" + n[1] + "</span></div>";
				else
					return "<div class=\"countdown_num\"><span>" + n[0] + "</span><span>" + n[1] + "</span><span>" + n[2] + "</span></div>";
			  }
		},
		CountBack:function( secs, self ){
			 if (secs < 0) {
				self.element.innerHTML = '<div class="tv-dealine"> '+self.options.deaLine+"</div>";
				return;
			  }
			  clearInterval(self.timer);
			  DisplayStr = self.options.displayFormat.replace(/%%D%%/g, self.calculateDate( secs,86400,100000) );
			  DisplayStr = DisplayStr.replace(/%%H%%/g, self.calculateDate(secs,3600,24));
			  DisplayStr = DisplayStr.replace(/%%M%%/g, self.calculateDate(secs,60,60));
			  DisplayStr = DisplayStr.replace(/%%S%%/g, self.calculateDate(secs,1,60));
			  self.element.innerHTML = DisplayStr;
			  if (self.options.countActive) {
				   self.timer = null;
				 self.timer =  setTimeout( function(){
					self.CountBack((secs+self.CountStepper),self);			
				},( self.SetTimeOutPeriod ) );
			 }
		}
					
	})
})(jQuery)