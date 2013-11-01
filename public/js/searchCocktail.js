/**
 * Created with JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/20/13
 * Time: 12:50 AM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
    var limit  = 3;
    var offset  = 0;

    $('#tags').bind('input', function(){

        $("#request").show();
        var value = $("#tags").val();
        offset = 0;
        $("#cocktailList").html("").append("<div class='row'></div>");
        request(value, limit, offset);
    });

    $("#request").click(function(){
        offset += limit;
        var value = $("#tags").val();
        request(value, limit, offset);


    });

});

function request(value, limit, offset)
{
    jQuery.ajax({
        url: 'http://localhost/cocktails/public/cocktail/load',
        type: 'POST',
        data: {param1: value, param2:limit, param3: offset },
        dataType: "json",
        success: function(result){
            var lenght = $(".row:last-child").children("div").length;

            jQuery.each( result, function(i,val) {
                if(val != "")
                {
                    jQuery.each( val, function(j,val_j) {
                        if(lenght < 6)
                        {
                            lenght++;
                        }
                        else
                        {
                            lenght=0;
                            $("#cocktailList").append("</div><div class='row'>");
                        }
                        $("#cocktailList .row:last-child").append("<div class='span2'><div class='thumbnail'><a href='cocktail/details/" + val_j.idCocktail + "'><img src='http://localhost/cocktails/data/cocktailImages/" + val_j.cocktailImageAdress + "' style='height:200px;' /><p>" + val_j.cocktailName + "</p></a></div></div>");
                    });
                }
                else
                {
                    $("#request").hide();
                }
            });
        }

    });
}