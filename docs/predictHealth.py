import sys
import pandas as pd
from sklearn.tree import DecisionTreeClassifier

# Map string labels to numerical values
label_mapping = {
    'Excellent': 4,
    'Good': 3,
    'Fair': 2,
    'Poor': 1,
    'Very Smooth': 4,
    'Smooth': 3,
    'Average': 2,
    'Difficult': 1,
    'Very Stable': 4,
    'Stable': 3,
    'Slightly Unstable': 2,
    'Unstable': 1,
    'Very Easy': 4,
    'Easy': 3,
    'Moderate': 2,
    'Difficult': 1,
    'Very Bumpy': 1,
    'Slightly Bumpy': 2,
    'Smooth': 3,
    'Very Smooth': 4
}

# Get input values from command line arguments
breakFunction, smoothPedaling, tireCondition, gearShifting, frameStability, handlebarControl, overallSmoothness = sys.argv[1:]

# Create a DataFrame with input values
data = pd.DataFrame({
    'Break Function': [breakFunction],
    'Smooth Pedaling': [smoothPedaling],
    'Tire Condition': [tireCondition],
    'Gear Shifting': [gearShifting],
    'Frame Stability': [frameStability],
    'Handlebar Control': [handlebarControl],
    'Overall Smoothness': [overallSmoothness]
})

# Map labels to numerical values
data.replace(label_mapping, inplace=True)

# Load the trained model (assuming you have saved it as a joblib file)
import joblib
model = joblib.load(r'D:\Projects\V-Rides\Documentation\trained_model.joblib')  # Adjust the path

# Predict the health score
predicted_health = model.predict(data)[0]

print(predicted_health)
