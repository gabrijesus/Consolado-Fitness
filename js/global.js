function menuResponsivo() {
    var mobile = document.querySelector('.mobile');
        if (mobile.classList.contains('open')) {
            mobile.classList.remove('open');
            document.querySelector('.icon_botao').src = "../imagens/menu02.png"
        } else {
            mobile.classList.add('open');
            document.querySelector('.icon_botao').src = "../imagens/fechar.png"
        }
}