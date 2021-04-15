-- Caution! Order of actions is important!
-- We need to create a dependent table first, only then the other table with binding fields

-- Рабочие смены
CREATE TABLE IF NOT EXISTS `b_sladcovich_worksheet_entity_orm_worksheet`
(
    `ID` int PRIMARY KEY AUTO_INCREMENT NOT NULL COMMENT 'ID',
    `DATETIME_START` datetime NOT NULL COMMENT 'Время начала смены',
    `DATETIME_END` datetime NOT NULL COMMENT 'Время окончания смены',
    `B24_COMPANY_ID` int unsigned NOT NULL COMMENT 'ID Компании (Битрикс 24)',
    `B24_MODIFIED_BY_USER_ID` int NOT NULL COMMENT 'ID пользователя (Битрикс 24)',
    `DATETIME_MODIFY` datetime NOT NULL COMMENT 'Дата и время изменения',

    `SEARCH_USER` varchar(255) NOT NULL COMMENT 'ФИО пользователя (Битрикс 24)',
    `SEARCH_COMPANY` varchar(255) NOT NULL COMMENT 'Наименование компании',
    `SEARCH_WORKERS` varchar(255) NOT NULL COMMENT 'ФИО работников',

    CONSTRAINT `sladcovich_worksheet_worksheet_b24_company_id_FK`
        FOREIGN KEY (`B24_COMPANY_ID`) REFERENCES `b_crm_company` (`ID`),
    CONSTRAINT `sladcovich_worksheet_worksheet_b24_modified_by_user_id_FK`
        FOREIGN KEY (`B24_MODIFIED_BY_USER_ID`) REFERENCES `b_user` (`ID`)
) COMMENT='Рабочие смены';

-- Рабочие
CREATE TABLE IF NOT EXISTS `b_sladcovich_worksheet_entity_orm_worker`
(
    `ID` int PRIMARY KEY AUTO_INCREMENT NOT NULL COMMENT 'ID',
    `WORKSHEET_ID` int NOT NULL COMMENT 'ID рабочей смены',
    `B24_CONTACT_ID` int unsigned NOT NULL COMMENT 'ID Контакта (Битрикс 24)',
    `B24_MODIFIED_BY_USER_ID` int NOT NULL COMMENT 'ID пользователя (Битрикс 24)',
    `DATETIME_MODIFY` datetime NOT NULL COMMENT 'Дата и время изменения',
    CONSTRAINT `sladcovich_worksheet_worker_worksheet_id_FK`
        FOREIGN KEY (`WORKSHEET_ID`) REFERENCES `b_sladcovich_worksheet_entity_orm_worksheet` (`ID`),
    CONSTRAINT `sladcovich_worksheet_worker_b24_contact_id_FK`
        FOREIGN KEY (`B24_CONTACT_ID`) REFERENCES `b_crm_contact` (`ID`),
    CONSTRAINT `sladcovich_worksheet_worker_b24_modified_by_user_id_FK`
        FOREIGN KEY (`B24_MODIFIED_BY_USER_ID`) REFERENCES `b_user` (`ID`)
) COMMENT='Рабочие';
