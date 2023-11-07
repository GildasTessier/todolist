CREATE TABLE task(
   id_task INT,
   name_task VARCHAR(50),
   date_create_task DATETIME,
   description_task VARCHAR(300),
   state_task BOOLEAN,
   priority_task SMALLINT,
   PRIMARY KEY(id_task)
);

INSERT INTO task (name_task, date_create, description_task, state_task, priority_task)
VALUES ("faire mon lit", "2023-11-07 10:00:35","fair mon lit au carré ","false","NULL"),
("éplucher les pommes de terre", "2023-11-07 11:00:50", "Eplucher 10 pommes de terre, les laver puis les coupers en frittes", "false", "NULL"),
("laver la voiture de papa", "2023-11-07 11:30:45","aspirer l'intérieur de la voiture et laver l'extéerieur avec le jet d'eau", "false", "NULL")
("ranger ma chambre", "2023-11-07 11:50:20", "ranger mes jouets dans l'armoire", "false", "NULL")
("nourrir le chat", "2023-11-07 12:01:05", "remplir la gamelle avec les croquettes et remettre de l'eau", "false", "NULL")
("brosser mes dents", "2023-11-07 13:10:25", "prendant 3min je brosse mes dents de haut en bas", "false", "NULL")
