USE 2023fall-comp307-mlavre1;
SET FOREIGN_KEY_CHECKS = 0;

-- 
-- Table structure
-- 

DROP TABLE IF EXISTS groups;
CREATE TABLE groups (
    group_id int NOT NULL AUTO_INCREMENT,
    group_name varchar(64) NOT NULL,
    is_staff boolean NOT NULL,
    PRIMARY KEY (group_id),
    UNIQUE (group_name)
);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    user_id int NOT NULL AUTO_INCREMENT,
    user varchar(64) NOT NULL,
    pass varchar(64) NOT NULL,
    first_name varchar(64) NOT NULL,
    last_name varchar(64) NOT NULL,
    email varchar(64) NOT NULL,
    group_id int NOT NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (group_id) REFERENCES groups(group_id),
    UNIQUE (user),
    UNIQUE (email)
);

DROP TABLE IF EXISTS boards;
CREATE TABLE boards (
    board_id int NOT NULL AUTO_INCREMENT,
    board_name varchar(64) NOT NULL,
    PRIMARY KEY (board_id)
);

DROP TABLE IF EXISTS channels;
CREATE TABLE channels (
    channel_id int NOT NULL AUTO_INCREMENT,
    channel_name varchar(64) NOT NULL,
    board_id int NOT NULL,
    PRIMARY KEY (channel_id),
    FOREIGN KEY (board_id) REFERENCES boards(board_id),
    UNIQUE (board_id, channel_name)
);

DROP TABLE IF EXISTS messages;
CREATE TABLE messages (
    message_id int NOT NULL AUTO_INCREMENT,
    message_title varchar(64) NOT NULL,
    message_text varchar(32768) NOT NULL,
    message_time datetime NOT NULL DEFAULT current_timestamp(),
    user_id int NOT NULL,
    channel_id int NOT NULL,
    PRIMARY KEY (message_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (channel_id) REFERENCES channels(channel_id)
);

DROP TABLE IF EXISTS board_users;
CREATE TABLE board_users (
    user_id int NOT NULL,
    board_id int NOT NULL,
    is_board_admin BOOLEAN NOT NULL,
    UNIQUE (user_id, board_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (board_id) REFERENCES boards(board_id)
);

DROP TABLE IF EXISTS board_applicants;
CREATE TABLE board_applicants (
    user_id int NOT NULL,
    board_id int NOT NULL,
    UNIQUE (user_id, board_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (board_id) REFERENCES boards(board_id)
);

DROP TABLE IF EXISTS channel_users;
CREATE TABLE channel_users (
    user_id int NOT NULL,
    channel_id int NOT NULL,
    UNIQUE (user_id, channel_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (channel_id) REFERENCES channels(channel_id)
);

DROP TABLE IF EXISTS errors;
CREATE TABLE errors (
    error_code int NOT NULL,
    error_description varchar(256) NOT NULL,
    PRIMARY KEY (error_code),
    UNIQUE (error_description)
);

-- 
-- Views for retrieving data
-- 

DROP VIEW IF EXISTS board_members;
CREATE VIEW board_members AS
SELECT boards.board_id, users.user, groups.group_name, board_users.is_board_admin
FROM boards
INNER JOIN board_users ON boards.board_id=board_users.board_id
INNER JOIN users ON users.user_id=board_users.user_id
INNER JOIN groups ON users.group_id=groups.group_id;

DROP VIEW IF EXISTS join_requests;
CREATE VIEW join_requests AS
SELECT boards.board_id, users.user, groups.group_name
FROM boards
INNER JOIN board_applicants ON boards.board_id=board_applicants.board_id
INNER JOIN users ON users.user_id=board_applicants.user_id
INNER JOIN groups ON users.group_id=groups.group_id;

DROP VIEW IF EXISTS channel_members;
CREATE VIEW channel_members AS
SELECT channels.channel_id, users.user, groups.group_name
FROM channels
INNER JOIN channel_users ON channels.channel_id=channel_users.channel_id
INNER JOIN users ON users.user_id=channel_users.user_id
INNER JOIN groups ON users.group_id=groups.group_id;

DROP VIEW IF EXISTS channel_messages;
CREATE VIEW channel_messages AS
SELECT channels.board_id, channels.channel_id, channels.channel_name, users.user, groups.group_name, messages.message_title, messages.message_text, messages.message_time
FROM messages
INNER JOIN channels ON messages.channel_id=channels.channel_id
INNER JOIN users ON messages.user_id=users.user_id
INNER JOIN groups ON users.group_id=groups.group_id
ORDER BY messages.message_time DESC;

DROP VIEW IF EXISTS visible_messages;
CREATE VIEW visible_messages AS
SELECT users.user, channel_messages.board_id, channel_messages.channel_id, channel_messages.channel_name, channel_messages.user AS author, channel_messages.group_name, channel_messages.message_title, channel_messages.message_text, channel_messages.message_time
FROM channel_messages
INNER JOIN channel_users ON channel_users.channel_id=channel_messages.channel_id
INNER JOIN users ON users.user_id=channel_users.user_id
ORDER BY channel_messages.message_time DESC;

DROP VIEW IF EXISTS user_boards;
CREATE VIEW user_boards AS
SELECT users.user, boards.board_id, boards.board_name, board_users.is_board_admin, channels.channel_id, channels.channel_name
FROM users
INNER JOIN board_users ON users.user_id=board_users.user_id
INNER JOIN boards ON board_users.board_id=boards.board_id
INNER JOIN channel_users ON channel_users.user_id=users.user_id
INNER JOIN channels ON channel_users.channel_id=channels.channel_id AND channels.board_id=boards.board_id;

DROP VIEW IF EXISTS user_info;
CREATE VIEW user_info AS
SELECT users.first_name, users.last_name, users.email, users.user, users.pass, groups.group_name, groups.is_staff
FROM users
INNER JOIN groups on users.group_id=groups.group_id;

--
-- Stored functions for storing data
--

DELIMITER //
DROP FUNCTION IF EXISTS registerUser//
CREATE FUNCTION registerUser(user varchar(64), pass varchar(64), first_name varchar(64), last_name varchar(64), email varchar(64), group_id int) RETURNS INT
BEGIN
    IF EXISTS(SELECT users.user FROM users WHERE users.user=user)
    THEN
        RETURN 1;
    ELSEIF EXISTS(SELECT users.email FROM users WHERE users.email=email)
    THEN
        RETURN 2;
    END IF;

    INSERT INTO users (`user`, `pass`, `first_name`, `last_name`, `email`, `group_id`) VALUES
    (user, pass, first_name, last_name, email, group_id);
    RETURN 0;
END //

DROP FUNCTION IF EXISTS createBoard//
CREATE FUNCTION createBoard(user VARCHAR(64), board_name VARCHAR(64)) RETURNS INT
BEGIN
    DECLARE board_id INT;
    IF (SELECT user_info.is_staff FROM user_info WHERE user_info.user=user LIMIT 1)
    THEN
        INSERT INTO boards (`board_name`) VALUES (board_name);
        SELECT boards.board_id FROM boards WHERE boards.board_name=board_name
        ORDER BY boards.board_id DESC LIMIT 1 INTO board_id;
        INSERT INTO board_users (`user_id`, `board_id`, `is_board_admin`) VALUES
        (
            (SELECT users.user_id FROM users WHERE users.user=user LIMIT 1),
            board_id,
            1
        );
        RETURN createChannel(user, board_id, 'Announcements');
    ELSE
        RETURN 3;
    END IF;
END //

DROP FUNCTION IF EXISTS createChannel//
CREATE FUNCTION createChannel(user VARCHAR(64), board_id INT, channel_name VARCHAR(64)) RETURNS INT
BEGIN
    DECLARE channel_id INT;
    IF EXISTS(SELECT channel_id FROM channels WHERE channels.board_id=board_id AND channels.channel_name=channel_name)
    THEN
        RETURN 4;
    ELSEIF NOT EXISTS(SELECT boards.board_id FROM boards WHERE boards.board_id=board_id)
    THEN
        RETURN 5;
    ELSEIF EXISTS(
        SELECT users.user_id FROM users
        INNER JOIN board_users ON board_users.user_id=users.user_id
        WHERE users.user=user AND board_users.board_id=board_id
    ) THEN
        INSERT INTO channels (`channel_name`, `board_id`) VALUES (channel_name, board_id);
        SELECT channels.channel_id FROM channels
        WHERE channels.channel_name=channel_name AND channels.board_id=board_id
        LIMIT 1 INTO channel_id;
        INSERT INTO channel_users (`user_id`, `channel_id`)
            SELECT board_users.user_id, channel_id FROM board_users
            WHERE board_users.board_id=board_id;
        RETURN 0;
    ELSE
        RETURN 6;
    END IF;
END //

DROP FUNCTION IF EXISTS sendMessage//
CREATE FUNCTION sendMessage(user VARCHAR(64), channel_id INT, message_text VARCHAR(32768), message_title VARCHAR(64)) RETURNS INT
BEGIN
    DECLARE user_id INT;
    SELECT users.user_id FROM users
    INNER JOIN channel_users ON users.user_id=channel_users.user_id
    WHERE users.user=user AND channel_users.channel_id=channel_id INTO user_id;
    IF user_id IS NOT NULL THEN
        INSERT INTO messages (`message_title`, `message_text`, `user_id`, `channel_id`) VALUES
        (message_title, message_text, user_id, channel_id);
        RETURN 0;
    ELSE
        RETURN 7;
    END IF;
END //

DROP FUNCTION IF EXISTS joinBoard//
CREATE FUNCTION joinBoard(user VARCHAR(64), board_id INT) RETURNS INT
BEGIN
    DECLARE user_id INT;
    SELECT users.user_id FROM users
    WHERE users.user=user INTO user_id;
    IF EXISTS(
        SELECT board_users.user_id FROM board_users
        WHERE board_users.user_id=user_id AND board_users.board_id=board_id
    ) THEN
        RETURN 8;
    ELSEIF EXISTS(
        SELECT board_applicants.user_id FROM board_applicants
        WHERE board_applicants.user_id=user_id AND board_applicants.board_id=board_id
    ) THEN
        RETURN 9;
    ELSE
        INSERT INTO board_applicants (`user_id`, `board_id`) VALUES
        (user_id, board_id);
        RETURN 0;
    END IF;
END //

DROP FUNCTION IF EXISTS approve//
CREATE FUNCTION approve(user VARCHAR(64), board_id INT) RETURNS INT
BEGIN
    DECLARE user_id INT;
    SELECT users.user_id FROM users
    WHERE users.user=user INTO user_id;
    IF EXISTS(
        SELECT board_users.user_id FROM board_users
        WHERE board_users.user_id=user_id AND board_users.board_id=board_id
    ) THEN
        RETURN 8;
    ELSEIF EXISTS(
        SELECT board_applicants.user_id FROM board_applicants
        WHERE board_applicants.user_id=user_id AND board_applicants.board_id=board_id
    ) THEN
        INSERT INTO board_users (`user_id`, `board_id`) VALUES
        (user_id, board_id);
        INSERT INTO channel_users (`user_id`, `channel_id`)
            SELECT user_id, channels.channel_id FROM channels
            WHERE channels.board_id=board_id;
        DELETE FROM board_applicants
        WHERE board_applicants.user_id=user_id AND board_applicants.board_id=board_id;
        RETURN 0;
    ELSE
        RETURN 10;
    END IF;
END //

DROP FUNCTION IF EXISTS reject//
CREATE FUNCTION reject(user VARCHAR(64), board_id INT) RETURNS INT
BEGIN
    DECLARE user_id INT;
    SELECT users.user_id FROM users
    WHERE users.user=user INTO user_id;
    IF EXISTS(
        SELECT board_users.user_id FROM board_users
        WHERE board_users.user_id=user_id AND board_users.board_id=board_id
    ) THEN
        RETURN 8;
    ELSEIF EXISTS(
        SELECT board_applicants.user_id FROM board_applicants
        WHERE board_applicants.user_id=user_id AND board_applicants.board_id=board_id
    ) THEN
        DELETE FROM board_applicants
        WHERE board_applicants.user_id=user_id AND board_applicants.board_id=board_id;
        RETURN 0;
    ELSE
        RETURN 10;
    END IF;
END //

DROP FUNCTION IF EXISTS removeMember//
CREATE FUNCTION removeMember(user VARCHAR(64), board_id INT) RETURNS INT
BEGIN
    DECLARE user_id INT;
    SELECT users.user_id FROM users
    WHERE users.user=user INTO user_id;
    IF EXISTS(
        SELECT board_users.user_id FROM board_users
        WHERE board_users.user_id=user_id AND board_users.board_id=board_id
    ) THEN
        DELETE channel_users FROM channel_users INNER JOIN channels
        ON channels.channel_id=channel_users.channel_id
        WHERE channels.board_id=board_id AND channel_users.user_id=user_id;
        DELETE FROM board_users WHERE board_users.user_id=user_id AND board_users.board_id=board_id;
        RETURN 0;
    ELSE
        RETURN 11;
    END IF;
END //

DROP FUNCTION IF EXISTS deleteChannel//
CREATE FUNCTION deleteChannel(channel_id INT) RETURNS INT
BEGIN
    DELETE FROM messages WHERE messages.channel_id=channel_id;
    DELETE FROM channel_users WHERE channel_users.channel_id=channel_id;
    DELETE FROM channels WHERE channels.channel_id=channel_id;
    RETURN 0;
END //

DROP FUNCTION IF EXISTS deleteBoard//
CREATE FUNCTION deleteBoard(user VARCHAR(64), board_id INT) RETURNS INT
BEGIN
    IF NOT EXISTS (
        SELECT * FROM board_users
        INNER JOIN users ON board_users.user_id=users.user_id
        WHERE board_users.is_board_admin = 1 AND board_users.board_id=board_id AND users.user=user
    ) THEN
        RETURN 12;
    END IF;
    DELETE messages FROM messages INNER JOIN channels
    ON channels.channel_id=messages.channel_id WHERE channels.board_id=board_id;
    DELETE channel_users FROM channel_users INNER JOIN channels
    ON channels.channel_id=channel_users.channel_id WHERE channels.board_id=board_id;
    DELETE FROM channels WHERE channels.board_id=board_id;
    DELETE FROM board_applicants WHERE board_applicants.board_id=board_id;
    DELETE FROM board_users WHERE board_users.board_id=board_id;
    DELETE FROM boards WHERE boards.board_id=board_id;
    RETURN 0;
END //

DELIMITER ;


SET FOREIGN_KEY_CHECKS = 1;

-- 
-- Error table, must remain how it is
-- 

INSERT INTO errors (error_code, error_description) VALUES
(0, "Success"),
(1, "Username already in use"),
(2, "An account with that email address already exists"),
(3, "Only staff are allowed to create discussion boards"),
(4, "Channel name already taken"),
(5, "No such board exists"),
(6, "Only board members can create channels"),
(7, "Only channel members can send messages"),
(8, "User is already a board member"),
(9, "User already requested to join this board"),
(10, "User did not request to join this board"),
(11, "Cannot remove non board member"),
(12, "Only admins can delete discussion boards");

-- Groups should be kept intact as well

INSERT INTO groups (group_name, is_staff) VALUES
('Professor', 1),
('TA', 1),
('Student', 0),
('Other', 1);

--
-- Dumping testing data
--
/*
INSERT INTO boards (board_name) VALUES
('COMP 307'),
('SSMU');

INSERT INTO channels (channel_name, board_id) VALUES
('grading', 1),
('administration', 1),
('planning', 2),
('general', 2);


INSERT INTO users (user, pass, first_name, last_name, email, group_id) VALUES
('johndoe', '$2a$12$kudlGBCLA7Z0HbJvqOnGhekobTnbuXi2A2G.jH4jyQmsJYorcuuy2', 'John', 'Doe', 'johndoe@gmail.com', 1),         -- 12345
('janedoe', '$2a$12$CfxPsLylZWMZZvkKkEcNeOsvphihyRpzPrQeWdKU6WNmcQG9QwiPO', 'Jane', 'Doe', 'janedoe@gmail.com', 2),         -- 54321
('johnsmith', '$2a$12$Gfj.qVtKy8tFjKCh77TIIe.kjg34vhDPquoueUJYG5k94i5imBWEG', 'John', 'Smith', 'johnsmith@gmail.com', 3),   -- abcde
('janesmith', '$2a$12$.ra66Y.JMfmDQjEtrmcht.joFIv41RLY35KNPCtA5dqZZTMxxe2ie', 'Jane', 'Smith', 'janesmith@gmail.com', 4);   -- edcba

INSERT INTO messages (message_title, message_text, message_time, user_id, channel_id) VALUES
('Fall 2023', 'top secret grading things', '2023-11-13 08:23:46', 1, 1),
('PSA', 'i love administrating', '2023-11-21 12:13:46', 4, 2),
('This weekend', 'im gonna plan the biggest party ever', '2023-11-30 16:20:34', 2, 3),
('What a day!', 'i love this school!', '2023-12-05 10:02:28', 3, 4);

INSERT INTO board_users (user_id, board_id, is_board_admin) VALUES
(1, 1, 0),
(2, 1, 0),
(4, 1, 1),
(2, 2, 0),
(3, 2, 0),
(4, 2, 1);

INSERT INTO channel_users (user_id, channel_id) VALUES
(1, 1),
(1, 2),
(2, 1),
(4, 2),
(2, 3),
(4, 3),
(3, 4),
(2, 4);
*/