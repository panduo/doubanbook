#coding:utf-8
import urllib
import re
import MySQLdb
import sys
import chardet
from sys import argv
from bs4 import BeautifulSoup 
import string

class BUG():
	def __init__(self,tag,start):
		self.domain = "https://book.douban.com/tag/%s?start=%s&type=T" % (tag,start)
		self.tag = tag
		self.size = 20
		self.cate_id = 0
		self.start = start
		self.conn = MySQLdb.connect('127.0.0.1','root','123456','douban')
		self.cur = self.conn.cursor()
		self.cur.execute("SELECT VERSION()")

	def getInt(self,str):
		str = re.search(r'\d+',str).group()
		return int(str)

	def getHtml(self):
		reload(sys)    
		sys.setdefaultencoding('utf8')  
		page = urllib.urlopen(self.domain)
		html = page.read()
		encoding_dict = chardet.detect(html)
		web_code = encoding_dict['encoding']
		if web_code == 'utf-8' or web_code == 'UTF-8' or 'UTF-8' in web_code:
			html = html
		else:
			html = html.decode('gbk','ignore').encode('utf-8')
		return html

	def getcate(self):
		html = self.getHtml()
		reg='<a.*?href="/tag/(.*?)".*?>(.*?)</a>.*?<b>'
		# reg = '<h2 class="location">.*?</h2>.*?<ul.*?'
		# reg = "<a href='(.*?)'>(.*?)</a>"
		imgre = re.compile(reg)
		imglist = re.findall(imgre,html)
		try:
			self.cur.execute("select * from cate")
			cateList = self.cur.fetchall()
			cates = {}
			for row in cateList:
				cates[row[0]] = row[1]
		except Exception, e:
			print e

		sql = "insert into cate (name) values ('%s')"
		for img in imglist:
			#urllib.urlretrieve(imgurl,'%s.jpg' % x)
			tmpsql = sql%img[1]
			if img[1] not in cates.values():
				try:
					print tmpsql
					self.cur.execute(tmpsql)
					cate_id = self.cur.lastrowid
					cates[cate_id] = img[1]	
				except Exception, e:
					print e
 			else:
				pass
				# print "跳过%s" % img[1]
				
			# print img[1]
		print "采集类别完毕"
		self.conn.commit()
		return cates
		# self.next_page(html)
	def getdetail(self,cate_id):
		self.cate_id = cate_id
		html = self.getHtml()
		html = BeautifulSoup(html,'html.parser').select('.subject-item')

		if(len(html) == 0): 
			return
		
		sql = "insert into books (img,title,href,cate,price,num,publish,publish_date,score,author,intro) values ('%s','%s','%s',%d,'%s',%d,'%s','%s','%s','%s','%s')"
		x = 0
		for row in html:
			img = row.select('img')[0].attrs['src']
			title = row.select('.info h2 a')[0]
			href = title.attrs['href']
			title = title.contents
			if len(title) > 1:
				title = (title[0]).strip() + (title[1].contents[0]).strip()
			else:
				title = title[0].strip()

			try:
				pub = row.select('.pub')[0].contents[0].strip().split('/')[::-1]#出版信息	
			except Exception, e:
				pub = {}
				print e
			
			#倒转list: 
			#pub = list(reversed(pub))
			#pub = sorted(pub,cmp=None,key=None,reverse=True) true为降序
			if len(pub) >= 4:
				price = pub.pop(0).strip()
				date = pub.pop(0).strip()
				publisher = pub.pop(0).strip()
			else:
				price = 0
				date = ''
				publisher = ''
			author = "/".join(pub[::-1]).strip()	

			try:
				self.cur.execute('select id from books where cate = %d and title = "%s" and author = "%s"'%(cate_id,title,author))
				id = self.cur.fetchone()
				if id is not None and id > 0:
					break
			except Exception, e:
				# self.conn.rollback()
				print e

			try:
				rating_nums = row.select('.rating_nums')
				score = float(rating_nums[0].contents[0]) if len(rating_nums) > 0 else 0		
			except Exception, e:
				rating_nums = 0
				print e	
			
			try:
				pl_con = row.select('.pl')
				pl = self.getInt(pl_con[0].contents[0]) if len(pl_con) > 0 else pl_con[0].contents[0]		
			except Exception, e:
				pl_con = 0
				print e	
			
			try:
				intro_con = row.select('p')
				intro = intro_con[0].contents[0].strip() if len(intro_con) > 0 else ""		
			except Exception, e:
				intro_con = ''
				print e	
			
			try:
				# print sql%(img,title,href,int(cate_id),price,pl,publisher,date,score,author,intro)
				self.cur.execute(sql%(img,title,href,int(cate_id),price,pl,publisher,date,score,author,intro))
			except Exception, e:
				# self.conn.rollback()
				print e
			
			x += 1
		print "已录入%d条记录"%x
		self.conn.commit()
		self.next_page()
	def next_page(self):
		print "采集:%s:第%d页"%(self.tag,int(self.start/20)+1)
		BUG(self.tag,self.start+self.size).getdetail(self.cate_id)
		
		
if __name__ == '__main__':
	cates = BUG('?view=type&icn=index-sorttags-all',0).getcate()
	for key,cate in cates.items():
		if key > 0:
			BUG(cate,0).getdetail(key)



