/**
 * Created with JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/21/13
 * Time: 11:06 PM
 * To change this template use File | Settings | File Templates.
 */
$(function(){
    $('#search').bind('input', function(){

        $("#cocktailList").html("").append("<div class='row'></div>");
        request($("#search").val());

    });
});


function request(value)
{
    jQuery.ajax({
        url: 'http://localhost/cocktails/public/shaker/shake',
        type: 'POST',
        data: {param1: value},
        dataType: "json",
        success: function(result){
            var lenght = $(".row:last-child").children("div").length;

            jQuery.each( result, function(i,val) {
                if(val != "")
                {
                    if(lenght < 6)
                    {
                        lenght++;
                    }
                    else
                    {
                        lenght=0;
                        $("#cocktailList").append("</div><div class='row'>");
                    }
                    $("#cocktailList .row:last-child").append("<div class='span2'><div class='thumbnail'><a href='/cocktails/public/cocktail/details/" + val.idCocktail + "'><img src='http://localhost/cocktails/data/cocktailImages/" + val.cocktailImageAdress + "' style='height:200px;' /><p>" + val.cocktailName + "</p></a></div></div>");
                }
                else
                {
                    $("#request").hide();
                }
            });
        }

    });
}