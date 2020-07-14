console.log('report.js');

$('.report').on('click', function(e){
    e.preventDefault();

    let btn = this;
    let url = $(this).data('link');

    if('{{ app.user }}'){
        if($(this).hasClass('btn-danger') ){//c'est qu'il a déja été signalé par le user
            //retire juste le signalement sans ouvrir la modal
            report(url, btn);
        }
        else{//il n'a pas encore été signalé
            //ouvre la modal et ne fait la requete qu'au click sur 'Voui voui'
            e.preventDefault();
            $('#link_flag').on('click', function(){
                let cause = $('#cause').val();
                // console.log(cause);
                report(url, btn, cause);
                $('.modal-report .close').click();
                $('#link_flag').off();//pour éviter que le click se simule plusieurs fois automatiquement a la suite
            });
        }
    }
    
});


function report(url, btn, cause = null){
    btn.disabled = true;
    console.log(cause);
    $('#link_flag').attr("disabled", true);

    $.ajax({
        url : url ,
        data : {cause : cause},
        type : 'GET',
        dataType : 'json',
        success : function(result){
            
            if(result.code == 200){
                if(result.add == 'add'){
                    btn.dataset.target = '';
                    btn.classList.remove('btn-outline-danger');
                    btn.classList.add('btn-danger'); 
                    $('#cause').val('');
                }else if(result.add == 'remove'){
                    btn.dataset.target = '#modal-report';
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-outline-danger');
                }
                btn.disabled = false;
                $('#link_flag').attr("disabled", false);
            }
            else {
                window.alert(result.message);
            }
        },
        error : function(error){
            console.log(error);
            window.alert('Un probleme est survenu.');
        }

    });
    
}
