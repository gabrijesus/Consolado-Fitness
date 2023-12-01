function habilitarEdicao() {
    // Oculta as informações do perfil
    document.getElementById('perfil').style.display = 'none';

    // Exibe o formulário de edição
    document.getElementById('formEdicaoContainer').style.display = 'flex';

    // Preenche os campos de edição com as informações atuais
    document.getElementById('novoNome').value = document.getElementById('nomeUsuario').innerText;
    document.getElementById('novoEmail').value = document.getElementById('emailUsuario').innerText;
    document.getElementById('novoNivelTreino').value = document.getElementById('nivelTreino').innerText;
    document.getElementById('novaEscolhaTreino').value = document.getElementById('preferenciaTreino').innerText;
    document.getElementById('novoSexo').value = document.getElementById('sexo').innerText;
    document.getElementById('novaDataNasc').value = document.getElementById('novaDataNasc').innerText;
}

function cancelarEdicao() {
    // Exibe as informações do perfil
    document.getElementById('perfil').style.display = 'block';

    // Oculta o formulário de edição
    document.getElementById('formEdicaoContainer').style.display = 'none';
}

function salvarEdicao() {
    var novoNome = document.getElementById('novoNome').value;
    var novoEmail = document.getElementById('novoEmail').value;
    var novoNivelTreino = document.getElementById('novoNivelTreino').value;
    var novaEscolhaTreino = document.getElementById('novaEscolhaTreino').value;
    var novoSexo = document.getElementById('novoSexo').value;
    var novaDataNasc = document.getElementById('novaDataNasc').value;

    $.ajax({
        type: "POST",
        url: "../utils/atualizaPerfil.php",
        data: { novoNome: novoNome, novoEmail: novoEmail, novoNivelTreino: novoNivelTreino, novaEscolhaTreino: novaEscolhaTreino, novoSexo: novoSexo, novaDataNasc: novaDataNasc },
    });

    cancelarEdicao();
}
