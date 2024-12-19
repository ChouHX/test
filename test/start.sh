#!/bin/bash
###
 # @Author: ZXH
 # @Date: 2024-12-18 17:05:39
 # @LastEditors: Please set LastEditors
 # @LastEditTime: 2024-12-18 17:31:38
 # @Description: 
### 

# 启动服务
cd /usr/local/m2m_server
./m2m_task_server
./m2m_gps_server
./m2m_ts_server
./m2m_server
wait
# 启动 PHP 和 Apache
apache2-foreground
