set backup_dir=d:\m2m_server\data\db
set dbname=m2mdb30
set db_user=m2m
set db_pwd=m2m_2018
d:/appserv/mysql/bin/mysqldump --no-defaults -u%db_user% -p%db_pwd% %dbname% --ignore-table=%dbname%.device_flux --ignore-table=%dbname%.device_login_record --ignore-table=%dbname%.download_report --ignore-table=%dbname%.rtu_data --ignore-table=%dbname%.rtu_warning --ignore-table=%dbname%.term_alarm_record --ignore-table=%dbname%.term_login_record --ignore-table=%dbname%.term_net_mode_record --ignore-table=%dbname%.term_stat_info --ignore-table=%dbname%.term_task --ignore-table=%dbname%.term_task_detail --ignore-table=%dbname%.timed_term_task_detail > %backup_dir%\%dbname%_%date:~0,4%%date:~5,2%%date:~8,2%.sql
forfiles /p "%backup_dir%" /s /m %dbname%_20*.sql /d -7 /c "cmd /c del @path"