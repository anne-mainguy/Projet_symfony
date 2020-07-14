$('a.js-like').on("click", function(e){
    e.preventDefault()
    let url = this.href;

    let divParent = this.parentElement;
    let spanLikes = divParent.querySelector('span.js-likes');
    let spanDisikes = divParent.querySelector('span.js-dislikes');
    let imgLike = divParent.querySelector('img.js-img-like');
    let imgDislike = divParent.querySelector('img.js-img-dislike');

     $.ajax({
        url : url,
        type : 'GET',
        dataType : 'json',
        success : function(result){
            
            if(result.code == 200){
                //afficher le nouveau compte de likes et de dislikes
                spanLikes.textContent = result.likes;
                spanDisikes.textContent = result.dislikes;

                //met les 2 icons en noir
                imgLike.src = '/img/icon/like.png';
                imgDislike.src = '/img/icon/dislike.png';
                //en fonction du nouveau vote, affiche le bon icon en couleur ou aucun
                if(result.voteValue == 1 ){
                    imgLike.src = '/img/icon/likeActive.png';
                }
                if(result.voteValue == 2){
                    imgDislike.src = '/img/icon/dislikeActive.png';
                }
            }
            else {
                window.alert('Vous devez être connecté pour voter.');
            }

        },
        error : function(error){
            console.log('marche pas');
            console.log(error);
        }

    });

});

$('div.js-not-link').on("click", function(){
    window.alert("Vous ne pouvez pas voter.");

});
