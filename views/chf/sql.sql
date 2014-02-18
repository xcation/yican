CREATE TABLE IF NOT EXISTS chfUserInfo (
	userId  varchar(50) primary key ,
	passwd 	varchar(50),
	sessionId varchar(80),
	expireTime timestamp
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS chfQuizInfo(
	quizId  int primary key auto_increment,
	quizTitle varchar(50),
	quizDecription varchar(200),
	quizImageUrl 	varchar(50),
	quizCreater 	varchar(50),
	quizCreateTime 	timestamp,
	quizLastTime 	int #minite
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS chfQuizQuestion(
    questionId int primary key ,
	quizId int
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS chfQuestionDetails(
    questionId int primary key auto_increment,
	questionText varchar(200),
	questionScore int,
	numbers 	 int DEFAULT 0,
	answerText_1 varchar(50),
	answerText_2 varchar(50),
	answerText_3 varchar(50),
	answerText_4 varchar(50),
	answerText_5 varchar(50),
	correct_1 	 char,
	correct_2 	 char,
	correct_3 	 char,
	correct_4 	 char,
	correct_5 	 char,
	orderNumber_1 int,
	orderNumber_2 int,
	orderNumber_3 int,
	orderNumber_4 int,
	orderNumber_5 int
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS chfQuizInvited(
    quizId int,
	invitedId varchar(50),
	isSubmited int,
	totalScore int
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE chfQuizInvited add constraint fk_invited_quizId foreign key (quizId) references chfQuizInfo(quizId);

ALTER TABLE chfQuizInvited add constraint fk_invited_invited
foreign key (invitedId) references chfUserInfo(userId);

ALTER TABLE chfQuizQuestion add constraint fk_qzqs_quesId
foreign key (questionId) references chfQuestionDetails(questionId);

ALTER TABLE chfQuizQuestion add constraint fk_qzqs_quizId
foreign key (quizId) references chfQuizInfo(quizId);


CREATE TABLE IF NOT EXISTS chfCheckIn(
    checkInId int primary key auto_increment,
    createUserId varchar(50),
    startTime timestamp,
    endTime timestamp,
    inviteCode int
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS chfCheckInInvited(
    checkInId int,
	invitedId varchar(50),
	isCheckIn int,
	checkInTime timestamp
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE chfCheckInInvited add constraint fk_chkin_id
foreign key (checkInId) references chfCheckIn(checkInId);
ALTER TABLE chfCheckInInvited add constraint fk_chkin_invited
foreign key (invitedId) references chfUserInfo(userId);
ALTER TABLE chfCheckIn add constraint fk_chkin_userid
foreign key (createUserId) references chfUserInfo(userId);
