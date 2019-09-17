from pylibdmtx.pylibdmtx import decode
from PIL import Image
import sys
import re
import base64
from io import BytesIO


try:
    barcode_file = sys.argv[1]
except Exception:
    sys.exit('ERROR')


if barcode_file is None:
    sys.exit('ERROR')

try:
    barcode = decode(Image.open(barcode_file))
    res = barcode[0].data.decode()

    print(res, '')
except Exception:
    print('ERROR')

