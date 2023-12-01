<!-- menu de navegação  -->
<nav class="navegacao">
    <div class="logo">
        <img src="../imagens/logo-mobile.png" alt="logo-mobile">
    </div>

    <div class="nav_desktop">
        <ul>
            <li class="nav"><a href="geraTreino.php" class="link">Treino</a></li>
            <li class="nav"><a href="bibliotecaExercicios.php" class="link">Biblioteca</a></li>
            <li class="nav"><a href="contato.php" class="link">Contato</a></li>
            <li class="nav"><a href="perfil.php" class="link">Perfil</a></li>
            <li class="nav" style="display: flex; justify-content: space-between; width: 65px; align-items: center;">
                <button onclick="location.href='../utils/logout.php'" class="link" style="background: none; cursor: pointer; display: flex; width: 65px; justify-content: space-between;">
                    <img src="../imagens/botao-logout.png" alt="" style="width: 25px;">
                    Sair</button>
            </li>
        </ul>
    </div>

    <div class="icon_menu">
        <button onclick="menuResponsivo()"><img class="icon_botao" src="../imagens/menu02.png" alt="menu"></button>
    </div>
</nav>

<div class="mobile">
    <ul>
        <li class="nav"><a href="geraTreino.php" class="link">Treino</a></li>
        <li class="nav"><a href="bibliotecaExercicios.php" class="link">Biblioteca</a></li>
        <li class="nav"><a href="contato.php" class="link">Contato</a></li>
        <li class="nav"><a href="perfil.php" class="link">Perfil</a></li>
        <li class="nav" style="display: flex; justify-content: space-between; width: 65px; align-items: center;">
                <button onclick="location.href='../utils/logout.php'" class="link" style="background: none; cursor: pointer; display: flex; width: 65px; justify-content: space-between;">
                    <img src="../imagens/botao-logout.png" alt="" style="width: 25px;">
                    Sair</button>
            </li>
    </ul>
</div>

<script src="../js/global.js"></script>
<!-- fim do menu navegacao  -->