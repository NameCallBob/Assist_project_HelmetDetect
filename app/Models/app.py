from flask import Flask, request, jsonify, abort, current_app
import mysql.connector
from ultralytics import YOLO
import os
import cv2
import base64

app = Flask(__name__)


model_path = os.path.join(os.path.dirname(__file__), 'helmet.pt')
model = YOLO('helmet.pt')
names = model.names

# 設置結果文件夾的相對路徑
app.config['RESULTS_FOLDER'] = os.path.join('..','..', 'storage', 'app','public', 'models_results')

# 將檔案轉換為二進位資料的函數
def convert_to_binary_data(file):
    binary_data = file.read()
    return binary_data

# 將二進位資料插入MySQL資料庫的函數
def insert_blob(image_data):
    try:
        connection = mysql.connector.connect(
            host='localhost',
            database='erp',
            user='root',
            password=''
        )
        cursor = connection.cursor()
        sql_insert_blob_query = """INSERT INTO image (image, date) VALUES (%s, NOW())"""
        cursor.execute(sql_insert_blob_query, (image_data,))
        connection.commit()
        print("成功將圖片作為BLOB插入到image表中,並附帶當前日期")
    except mysql.connector.Error as error:
        print(f"插入BLOB數據到MySQL表失敗 {error}")
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL連接已關閉")

# 檢測上傳圖片中是否有未戴安全帽
def detect_nohelmet(image_path):
    frame = cv2.imread(image_path)
    results = model.predict(frame, verbose=False)
    frame = results[0].plot()
    _, buffer = cv2.imencode('.jpg', frame)
    image_base64 = base64.b64encode(buffer).decode('utf-8')
    for data in results[0].boxes.data:
        n = names[int(data[5])]
        if n == 'nohelmet':
            return True, frame, image_base64
    return False, frame, image_base64

# 提供API說明或端點列表的端點
@app.route('/', methods=['GET'])
def index():
    return jsonify(message='歡迎使用圖片上傳API。使用/images端點上傳圖片。')

# 上傳圖片的端點
@app.route('/images', methods=['POST'])
def upload_image_api():
    if 'image' not in request.files:
        return jsonify(message='請求中缺少圖片部分'), 400

    file = request.files['image']
    if file.filename == '':
        return jsonify(message='未選擇檔案'), 400

    if file:
        file_path = os.path.join('uploads', file.filename)
        file.save(file_path)

        no_helmet_detected, processed_frame, image_base64 = detect_nohelmet(file_path)
        if no_helmet_detected:
            with open(file_path, 'rb') as f:
                binary_image = convert_to_binary_data(f)
                # insert_blob(binary_image)
            # 使用 Flask 應用配置來設置結果文件路徑
            result_file_path = os.path.join(current_app.config['RESULTS_FOLDER'], file.filename)
            cv2.imwrite(result_file_path, processed_frame)

        os.remove(file_path)

        detection_result = True if no_helmet_detected else False
        return jsonify(message='辨識成功。', detection=detection_result,filename = str(file.filename)), 200
    else:
        return jsonify(message='圖片上傳失敗'), 400


if __name__ == "__main__":
    if not os.path.exists('uploads'):
        os.makedirs('uploads')
    results_folder = os.path.join('..','..', 'storage', 'app','public', 'models_results')
    if not os.path.exists(results_folder):
        os.makedirs(results_folder)
    app.run(debug=True)
