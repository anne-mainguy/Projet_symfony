    //récupérer l'url et la mettre en href des lien arrow + 1 ou 2 en fonction de l'ordre
    let urlCurrent = window.location.pathname;
    let urlPropre = urlCurrent.split('/');
    let offsetOrder;

    for(let i = 0; i < urlPropre.length; i++){
        if(!isNaN(urlPropre[i] )){
            offsetOrder = i;
        }
    }

    if(offsetOrder == 0){
        offsetOrder = urlPropre.length ;
    }

    function createUrl(order, offset){
        urlPropre.splice(offset, 1, order);
        let url = urlPropre.join('/');
        return url;
    }

    $('.arrow-up').attr('href', createUrl('2', offsetOrder));
    $('.arrow-down').attr('href' , createUrl('1', offsetOrder));
