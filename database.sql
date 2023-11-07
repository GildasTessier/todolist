CREATE TABLE task(
   id_task INT,
   name_task VARCHAR(50),
   date_create_task DATETIME,
   description_task VARCHAR(300),
   state_task BOOLEAN,
   priority_task SMALLINT,
   PRIMARY KEY(id_task)
);