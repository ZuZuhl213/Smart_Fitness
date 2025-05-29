from flask import Flask, request, jsonify
import pickle
import numpy as np
import os
import traceback
import pandas as pd
import csv

app = Flask(__name__)

# Load the trained model
MODEL_PATH = 'pipeline.pkl'  # Using pipeline.pkl

# Check if model file exists
if not os.path.exists(MODEL_PATH):
    raise FileNotFoundError(f"Model file '{MODEL_PATH}' not found. Please place your trained pipeline model in this directory.")

# Load the model
try:
    with open(MODEL_PATH, 'rb') as f:
        pipeline = pickle.load(f)
    print(f"Pipeline model loaded successfully from {MODEL_PATH}!")
except Exception as e:
    print(f"Error loading model: {e}")
    raise

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Get data from request
        data = request.json
        
        if not data:
            return jsonify({
                'error': 'No data provided'
            }), 400
            
        print(f"Received data: {data}")
        
        # Extract features
        gender = data.get('gender', 'male')  # giữ nguyên 'male' hoặc 'female'
        features = [
            gender,
            np.float64(data.get('age', 0)),
            np.float64(data.get('height', 0)),
            np.float64(data.get('weight', 0)),
            np.float64(data.get('duration', 0)),
            np.float64(data.get('heart_rate', 0)),
            np.float64(data.get('body_temp', 0))
        ]
        
        print(f"Extracted features: {features}")
        
        # Convert to pandas DataFrame with correct columns
        columns = ['Gender', 'Age', 'Height', 'Weight', 'Duration', 'Heart_Rate', 'Body_Temp']
        df = pd.DataFrame([features], columns=columns)
        # Make prediction using the pipeline
        prediction = pipeline.predict(df)
        
        print(f"Prediction result: {prediction[0]}")
        
        # Return prediction as JSON
        return jsonify({
            'calories': int(round(prediction[0])),
            'model_used': 'pipeline.pkl',
            'features': features
        })
    
    except Exception as e:
        error_traceback = traceback.format_exc()
        print(f"Error during prediction: {e}")
        print(error_traceback)
        return jsonify({
            'error': str(e),
            'traceback': error_traceback
        }), 500

@app.route('/recommend_menu', methods=['POST'])
def recommend_menu():
    try:
        data = request.json
        goal = data.get('goal')
        burned_calories = float(data.get('burned_calories', 0))
        tolerance = float(data.get('tolerance', 0.1))

        ratios = {
            'giảm_mỡ': 0.5,
            'giảm_cân': 0.6,
            'giữ_cân': 0.85,
            'tăng_cơ': 1.1,
            'tăng_cân': 1.2
        }

        if goal not in ratios:
            return jsonify({'error': 'Invalid goal'}), 400

        target = burned_calories * ratios[goal]
        min_cal = target * (1 - tolerance)
        max_cal = target * (1 + tolerance)

        results = []
        csv_path = 'menu_labeled.csv'
        if not os.path.exists(csv_path):
            return jsonify({'error': 'menu_labeled.csv not found'}), 500

        with open(csv_path, newline='', encoding='utf-8') as csvfile:
            reader = csv.DictReader(csvfile)
            for row in reader:
                try:
                    if (
                        row['goal'] == goal and
                        min_cal <= float(row['total_calories']) <= max_cal
                    ):
                        results.append({
                            'name': row['menu'],  # Đổi 'menu' thành 'name'
                            'calories': row['total_calories'],
                            'protein': row['total_protein'],
                            'fat': row['total_fat'],
                            'carbs': row['total_carb']
                        })
                except Exception:
                    continue

        return jsonify({'menus': results[:5]})

    except Exception as e:
        error_traceback = traceback.format_exc()
        print(f"Error during recommend_menu: {e}")
        print(error_traceback)
        return jsonify({'error': str(e), 'traceback': error_traceback}), 500

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'healthy', 
        'message': 'ML service is running',
        'model': 'pipeline.pkl'
    })

@app.route('/', methods=['GET'])
def home():
    return """
    <html>
        <head>
            <title>SmartFit ML Service</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                h1 { color: #0d6efd; }
                .container { max-width: 800px; margin: 0 auto; }
                .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
                .success { color: #198754; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>SmartFit ML Service</h1>
                <div class="card">
                    <h2 class="success">Service is running!</h2>
                    <p>The ML service for SmartFit is active and ready to process predictions.</p>
                    <p>Model in use: <strong>pipeline.pkl</strong></p>
                </div>
                <div class="card">
                    <h3>Available Endpoints:</h3>
                    <ul>
                        <li><strong>POST /predict</strong> - Make calorie predictions</li>
                        <li><strong>POST /recommend_menu</strong> - Recommend menu from CSV</li>
                        <li><strong>GET /health</strong> - Check service health</li>
                    </ul>
                </div>
            </div>
        </body>
    </html>
    """

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)