# Sentiment Analysis of Bukalapak Product Reviews

## 📌 About This Project

Marketplaces like Bukalapak play a crucial role in e-commerce, where product reputation is shaped by customer reviews. This project focuses on performing sentiment analysis on "kemeja pria" reviews using Natural Language Processing (NLP) techniques to classify sentiments into positive, neutral, or negative.

The model combines a Lexicon-Based method using the **InSet Lexicon** and a machine learning approach with **Multinomial Naïve Bayes**. It outperformed RapidMiner, achieving:

- 🎯 **Precision:** 94% (Positive Class)  
- 🔁 **Recall:** 99% (Positive Class)  
- ✅ **Accuracy:** Between 91% and 94%

While positive sentiments were mostly about product quality and fast delivery, negative ones addressed product defects and incorrect sizes.

---

## 🧠 Modeling Workflow

1. **Scraping**: Data was scraped from Bukalapak using the platform’s API, focusing on product review content.
2. **Storage**: Raw data (reviewer, title, content) is stored in a database.
3. **Preprocessing**:
   - Cleaning  
   - Case Folding  
   - Normalization  
   - Tokenizing  
   - Stopword Removal  
   - Stemming  
4. **Labeling**: Using InSet Lexicon to assign sentiment labels (Positive, Neutral, Negative).
5. **Splitting**: Dataset split into 5 training/testing scenarios (90:10, 80:20, etc.)
6. **Vectorization**: TF-IDF applied to measure word importance.
7. **Classification**: Performed using Multinomial Naïve Bayes.
8. **Evaluation**: Using confusion matrix, precision, recall, accuracy, and visualization tools like word cloud and bar charts.

---

## 📊 Visualization Outputs

- Word Cloud of frequently used sentiment words  
- Bar Chart of sentiment distribution  
- Confusion Matrix summary  
- Precision, Recall, and Accuracy metrics

---

## 📬 Contact

Feel free to reach out via [brianadambhagaskara@gmail.com](mailto:brianadambhagaskara@gmail.com) for collaboration or feedback.

---


