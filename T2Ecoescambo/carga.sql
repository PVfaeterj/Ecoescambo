USE ecoescambo;

INSERT INTO usuarios (id, nome, email, senha) VALUES
(1, 'Marcelo Belo', 'mbelo@teste.com.br', '$2y$12$W4gz/uQF6rrW/TAukrKCMexhSXqgLtyAgJ6YZn3SpOkPFy4QbVNSC'),
(2, 'Jamile', 'jamile@teste.com.br', '$2y$12$W4gz/uQF6rrW/TAukrKCMexhSXqgLtyAgJ6YZn3SpOkPFy4QbVNSC');

INSERT INTO produtos (usuario_id, titulo, categoria, marca, condicao, regiao, aceita_troca) VALUES
(1, 'Vaso de plantas', 'Móveis e Decoração', NULL, 'usado', 'centro', 'Livros ou artigos de decoração'),
(1, 'Kit de livros', 'Livros', NULL, 'usado', 'zonasul', 'Plantas ou jogos'),
(1, 'Garrafa reutilizável', 'Esporte e Lazer', NULL, 'usado', 'zonanorte', 'Livros'),
(1, 'Jogo de tabuleiro', 'Videogames e Consoles', NULL, 'usado', 'centro', 'Acessórios'),
(1, 'Mochila escolar', 'Roupas e Calçados', NULL, 'usado', 'zonaoeste', 'Livros ou roupas'),
(2, 'Jaqueta Jeans', 'Roupas e Calçados', 'Levis', 'usado', 'centro', 'Tênis ou mochila'),
(2, 'Violão acústico', 'Instrumentos Musicais', 'Yamaha', 'usado', 'zonasul', 'Livros ou eletrônicos'),
(2, 'Celular Samsung', 'Eletrônicos', 'Samsung', 'usado', 'zonanorte', 'Fone de ouvido'),
(2, 'Cadeira de escritório', 'Móveis e Decoração', NULL, 'novo', 'centro', 'Mesa de escritório'),
(2, 'Relógio digital', 'Eletrônicos', 'Casio', 'usado', 'zonasul', 'Acessórios');
