# importCsv
import csv to mysql
## 实现批量把csv大文件导入MySQL数据库；
## 所用到的知识：
* file_put_coontents() 获取文件内容
* preg_replace() 字符串匹配防止因excel导出为csv单元格存在换行符导致的数据错误。