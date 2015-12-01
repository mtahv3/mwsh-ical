$(document).ready(function(){
    $.ajax({ url: "league",
        context: document.body,
        success: function(response){
            var json= $.parseJSON(response);

            $.each(json, function(i, item){
                $.each(item.leagues, function(x, league){
                    $('#selectLeague').append($("<option>", {
                        value:league.id,
                        text: item.day + " - " + league.name
                    }));
                });
            });
        }}
    );

    $('#selectLeague').change(function(){
        var selectedLeague = $('#selectLeague').val();
        var selTeam=$('#selectTeam');
        selTeam.attr('disabled', 'disabled');

        $('#url').html("");
        if(selectedLeague != 0){
            $('#selectTeam').html("");
            $.ajax({url: "league/"+selectedLeague+"/teams",
                success: function(response){
                    var json = $.parseJSON(response);
                    $.each(json, function(i, item){
                        $('#selectTeam').append($("<option>", {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    selTeam.removeAttr('disabled');
                }
            });
        }
    });

    $('#generateButton').click(function(){
        var selectedLeague=$('#selectLeague').val();
        var selectedTeam=$('#selectTeam').val();

        if(selectedLeague != 0 && selectedTeam != 0){
            var url="http://"+window.location.host+"/schedule/league/"+selectedLeague+"/team/"+selectedTeam;

            $("#url").html( $("<a>", { href: url, text: url }));
        }
    });

    $('#selectTeam').attr('disabled', 'disabled');

});