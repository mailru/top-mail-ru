import struct
import urllib
import urllib2
import json
import hashlib

class TopMailRu(object):

  apikey = ""
  session = ""

  def __init__(self, apikey):
    self.apikey = apikey


  def request(self, path, args):
    url = 'http://top.mail.ru' + path + '?' + urllib.urlencode(args)
    try:
      resp = urllib2.urlopen(url)
      jr = json.loads(resp.read())
    except IOError, e:
      if hasattr(e, 'reason'):
        print 'We failed to reach a server.'
        print 'Reason: ', e.reason
      elif hasattr(e, 'code'):
        print 'The server couldn\'t fulfill the request.'
        print 'Error code: ', e.code
    else:
      return jr

  def registerSite(self, args):
    args['apikey'] = self.apikey
    args['session'] = self.session
    return self.request('/json/add', args)

  def editSite(self, id, password, args):
    args['apikey'] = self.apikey
    args['session'] = self.session
    args['id'] = id
    args['password'] = password
    return self.request('/json/edit', args)

  def getCode(self, id, password, args):
    args['apikey'] = self.apikey
    args['session'] = self.session
    args['id'] = id
    args['password'] = password
    return self.request('/json/code', args)

  def getStat(self, id, password, type, args):
    args['apikey'] = self.apikey
    args['session'] = self.session
    args['id'] = id
    args['password'] = password
    return self.request('/json/'+type, args)

  def setSession(self, session):
    self.session = session

  def login(self, id, password):
    args = { 'id': id, 'password': password, 'action': 'json' }
    args['apikey'] = self.apikey
    args['session'] = self.session
    res = self.request('/json/login', args)
    if res.get('session'):
      self.session = res['session']
    return res.get('logged') == 'yes'

  def loginByHash(self, id, ph):
    def sha1(values):
      sha1 = hashlib.sha1()
      for i in values:
        sha1.update(i)
      return sha1.digest()
    def strxor(rhs, lhs):
      return "".join(chr(ord(x) ^ ord(y)) for x, y in zip(rhs, lhs))
    args = { 'id': id, 'action': 'json' }
    args['apikey'] = self.apikey
    args['session'] = self.session
    res = self.request('/json/login', args)
    if res.get('session'):
      self.session = res['session']
    if res.get('salt') and res.get('seed'):
      hash2 = sha1((res['salt'], ph))
      hash3 = strxor(ph, sha1((res['seed'], hash2)))
      args = { 'id': id, 'action': 'json', 'ph': hash3.encode('hex'), 'seed': res['seed'] }
      args['apikey'] = self.apikey
      args['session'] = self.session
      res = self.request('/json/login', args)
    return res.get('logged') == 'yes'
