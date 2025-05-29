import pickle
import os
import pandas as pd

def test_model():
    MODEL_PATH = 'pipeline.pkl'  # Changed from 'ml_service/pipeline.pkl'
    
    if not os.path.exists(MODEL_PATH):
        print(f"Error: Model file '{MODEL_PATH}' not found.")
        return False
    
    try:
        with open(MODEL_PATH, 'rb') as f:
            pipeline = pickle.load(f)
        
        print(f"Model loaded successfully from {MODEL_PATH}")
        
        columns = ['Gender', 'Age', 'Height', 'Weight', 'Duration', 'Heart_Rate', 'Body_Temp']
        
        sample_data = [
            [1, 30, 175, 70, 45, 120, 37.2],
            [0, 25, 165, 60, 30, 110, 36.8],
            [1, 40, 180, 85, 60, 130, 37.5]
        ]
        
        df_sample = pd.DataFrame(sample_data, columns=columns)
        
        predictions = pipeline.predict(df_sample)
        for i, pred in enumerate(predictions):
            print(f"Sample {i+1}: {df_sample.iloc[i].tolist()} -> Prediction: {pred} calories")
        
        return True
    
    except Exception as e:
        print(f"Error testing model: {e}")
        return False

if __name__ == "__main__":
    success = test_model()
    if success:
        print("\nModel test completed successfully!")
    else:
        print("\nModel test failed!")