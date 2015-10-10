$(document).ready(function(){
  $("p.a").click(function(){
  	var a = js_context.site_sample_url;
  	$.ajax({
       url: a,
       type: 'post',
       data: {searchname: 123 , searchby:454},
       success: function (data) {
          console.log(data);
       }
  	});
  });
});

