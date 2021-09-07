from flask import *
from PIL import Image
from keras.models import load_model
import keras
from io import BytesIO
import sys, os
import numpy as np
from matplotlib import pyplot as plt
import base64

imsize = (64, 64)

keras_param = "/home/ec2-user/cnn_tamago.h5"
def load_image(img64):
    img = Image.open(BytesIO(base64.b64decode(img64)))
    img = img.convert('RGB')
    # 学習時に、(64, 64, 3)で学習したので、画像の縦・横は今回 変数imsizeの(64, 64)にリサイズします。
    img = img.resize(imsize)
    # 画像データをnumpy配列の形式に変更
    img = np.asarray(img)
    img = img / 255.0
    return img

def suiron(img64):
    model = load_model(keras_param)
    img = load_image(img64)
    prd = model.predict(np.array([img]))
#    print(prd) # 精度の表示
    prelabel = np.argmax(prd, axis=1)
    if prelabel == 0:
        kekka = "たまご以外です"
    elif prelabel == 1:
        kekka = "多分たまごです"
    return (kekka, prd)


app = Flask(__name__)

@app.route('/', methods=["GET", "POST"])
def index():
    if request.method == "GET":
        message = "<p>method is GET.</p>"
        message += "<p>Please POST a image file.</p>"

    elif request.method == "POST":
        kekka, prd = suiron(request.form["image"])
#        message = "<p>method is POST.</p>"
#        message = "<p>image: " + request.form["image"] + "</p>"
        message = "<p>" + kekka + "</p>"
        message += "<p>たまご度" + str(int(prd[0][1]*100)) + "%</p>"

    return message

if __name__ == '__main__':
    app.run(debug=False, host='0.0.0.0', port=5000)
