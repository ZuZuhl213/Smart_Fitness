# SmartFit ML Service

Đây là microservice AI cho ứng dụng SmartFit, sử dụng Flask để phục vụ model machine learning đã được train.

## Cài đặt

1. Đảm bảo bạn đã cài đặt Python 3.7 trở lên
2. Cài đặt các thư viện cần thiết:
   \`\`\`
   pip install -r requirements.txt
   \`\`\`

## Sử dụng

1. Đặt file model pipeline.pkl vào thư mục này
2. Chạy Flask API:
   \`\`\`
   python app.py
   \`\`\`
3. API sẽ chạy ở địa chỉ http://localhost:5000

## API Endpoints

### 1. Dự đoán Calories

**URL:** `/predict`
**Method:** POST
**Content-Type:** application/json

**Request Body:**
\`\`\`json
{
  "age": 30,
  "height": 175,
  "weight": 70,
  "duration": 45,
  "heart_rate": 120,
  "body_temp": 37.2
}
\`\`\`

**Response:**
\`\`\`json
{
  "calories": 350,
  "model_used": "pipeline.pkl"
}
\`\`\`

### 2. Kiểm tra trạng thái

**URL:** `/health`
**Method:** GET

**Response:**
\`\`\`json
{
  "status": "healthy",
  "message": "ML service is running",
  "model": "pipeline.pkl"
}
\`\`\`

## Cấu trúc Model

Model được sử dụng là một scikit-learn pipeline đã được train và lưu dưới dạng file pickle (.pkl). Pipeline này có thể bao gồm các bước tiền xử lý dữ liệu và mô hình dự đoán.

## Lưu ý

- Đảm bảo rằng model pipeline.pkl của bạn được train với đúng 6 features theo thứ tự: age, height, weight, duration, heart_rate, body_temp
- Nếu cấu trúc model của bạn khác, hãy điều chỉnh file app.py phù hợp
