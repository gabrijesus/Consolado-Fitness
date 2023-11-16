<?php 
    define("QUERY_SELECT_EXERCICIOS", "SELECT * FROM exercicios WHERE modalidade = ?");
    define("QUERY_INSERT_USER", "INSERT INTO usuarios (nome_usuario, email, senha, nivel_treino, preferencia_treino, data_nascimento, sexo) VALUES (?,?,?,?,?,?,?)");
    define("QUERY_INSERT_FICHA_TREINO", "INSERT INTO ficha_de_treino (nome_ficha_treino, exercicios) VALUES (?,?)");
    define("QUERY_SELECT_USER", "SELECT * FROM usuarios WHERE id_usuario = ?");
?>