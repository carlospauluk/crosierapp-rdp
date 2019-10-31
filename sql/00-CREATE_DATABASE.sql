DROP DATABASE IF EXISTS `crosier_rodoponta_dev`;
CREATE DATABASE `crosier_rodoponta_dev` CHARACTER SET 'utf8' COLLATE 'utf8_swedish_ci';
GRANT ALL PRIVILEGES ON crosier_rodoponta_dev.* TO 'crosier_rodoponta_dev'@'localhost' IDENTIFIED BY 'crosier_rodoponta_dev';
FLUSH PRIVILEGES;



-- mysql_config_editor set --login-path=crosier_rodoponta_dev --host=localhost --user=crosier_rodoponta_dev --password=crosier_rodoponta_dev

-- sudo vim /usr/local/bin/crosier_rodoponta_dev
-- mysql --login-path=crosier_rodoponta_dev crosier_rodoponta_dev

-- sudo chmod a+x /usr/local/bin/crosier_rodoponta_dev
