DROP TABLE IF EXISTS `USERS`;
DROP TABLE IF EXISTS `MESSAGES`;
DROP TABLE IF EXISTS `connection_errors`;

create table `USERS`(
id_user int(11) primary key AUTO_INCREMENT,
login varchar(10) unique ,
mot_de_passe  varchar(512),
nom  varchar(50),
prenom varchar(50),
numero_compte varchar(20),
profil_user varchar(5) ,
check (profil_user in ('ADMIN', 'USER')),
solde_compte int(11)
);

create table `MESSAGES`(
id_msg int(11) primary key AUTO_INCREMENT,
id_user_to int(11),
id_user_from int(11),
sujet_msg varchar(100),
corps_msg varchar(500)
);

create table `connection_errors`(
id_connection_errors int(11) primary key AUTO_INCREMENT,
ip varchar(50)  ,
error_date varchar(50)
);
-- !!! le mot de passe de utlisateur zilu est "123456". le mot de passe dans DB a ete password_hash par php!
insert into USERS values(null, "zilu", "$2y$10$ALGk9s6Z/tezcONKrn0Rs.fhoGT8UbpsvvS0YxaHaeApG1BcGH6M2", "zheng", "zilu", "FR0001", "ADMIN",100);
-- !!! le mot de passe de utlisateur nolan est "654321"
insert into USERS values(null, "nolan", "$2y$10$xr59k4heiJ6dDOEqc/jRQ..EX2HHGqbKFI1hqesDBZS4oC/HO5S4m", "zhou", "xianhua", "FR0002", "ADMIN",100);