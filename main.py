"""
构建简易 http 服务，接收传送过来的 base64图像扫描data matrix码并返回
last updated at 2019-9-17 23:29:26
"""
from aiohttp import web
from pylibdmtx.pylibdmtx import decode
from PIL import Image
import re
from io import BytesIO
import base64


async def handleGet(request):
    return web.Response(text="Access deny.")

async def handlePost(request):
    post_data = await request.post()

    if 'img' not in post_data:
        ret = {'errcode': 1000, 'msg': '缺失参数'}
        return web.json_response(ret)

    base64_str = re.sub('^data:image/.+;base64,', '', post_data['img'])

    barcode = decode(Image.open(BytesIO(base64.b64decode(base64_str))))
    res = barcode[0].data.decode()

    ret = {'errcode': 0, 'msg': 'success', 'data': res}
    return web.json_response(ret)


ret = {'errcode':0, 'msg':'', 'data':''}
app = web.Application()
app.add_routes([web.get('/', handleGet),web.post('/', handlePost)])

if __name__ == '__main__':
    web.run_app(app)