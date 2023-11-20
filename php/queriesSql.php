<?php 
    define("QUERY_SELECT_EXERCICIOS_FILTRO", "SELECT nome_exercicio FROM exercicio WHERE (modalidade = ? OR grupo_muscular = 'Abdomen') AND grupo_muscular = ? ORDER BY RAND() LIMIT ?");
    define("QUERY_SELECT_TODOS_EXERCICIOS", "SELECT * FROM exercicio");
    define("QUERY_SELECT_BUSCA","SELECT * FROM exercicio WHERE nome_exercicio LIKE ?");
    define("QUERY_INSERT_USER", "INSERT INTO usuarios (nome_usuario, email, senha, nivel_treino, preferencia_treino, data_nascimento, sexo) VALUES (?,?,?,?,?,?,?)");
    define("QUERY_INSERT_FICHA_TREINO", "INSERT INTO ficha_de_treino (nome_ficha_treino, exercicios) VALUES (?,?)");
    define("QUERY_SELECT_USER", "SELECT * FROM usuarios WHERE id_usuario = ?");
?>