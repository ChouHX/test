#!/bin/bash
backup_dir="/usr/local/m2m_server/data/db"
dbname="m2mdb30"
db_user="m2m"
db_pwd="m2m_2018"
mysqldump --no-defaults -u${db_user} -p${db_pwd} ${dbname} --ignore-table=${dbname}.device_flux --ignore-table=${dbname}.device_login_record --ignore-table=${dbname}.download_report --ignore-table=${dbname}.rtu_data --ignore-table=${dbname}.rtu_warning --ignore-table=${dbname}.term_alarm_record --ignore-table=${dbname}.term_login_record --ignore-table=${dbname}.term_net_mode_record --ignore-table=${dbname}.term_stat_info --ignore-table=${dbname}.term_task --ignore-table=${dbname}.term_task_detail --ignore-table=${dbname}.timed_term_task_detail > ${backup_dir}/${dbname}_`date +%Y%m%d`.sql
find $backup_dir -name "${dbname}_20*.sql" -type f -mtime +7 -exec rm -rf {} \;