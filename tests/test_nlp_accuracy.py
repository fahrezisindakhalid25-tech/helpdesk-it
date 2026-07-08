import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
"""
Script Evaluasi Akurasi NLP Engine — IT Helpdesk PTPN IV
Mengirim request nyata ke endpoint /predict dan menghitung metrik evaluasi.
"""

import csv
import json
import time
import requests
import sys
import os
from collections import defaultdict

NLP_URL = "http://127.0.0.1:8000/predict"
DATA_FILE = os.path.join(os.path.dirname(__file__), "data_uji.csv")
OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "hasil_evaluasi")

CATEGORIES_DB = [
    "Network",
    "Hardware",
    "Software",
    "Database",
    "Server",
    "Storage",
    "CCTV & Security Systems",
    "Web & Intranet Portal",
    "Keamanan & Virus",
    "Peripheral"
]

THRESHOLD = 0.20

def load_test_data(filepath):
    """Load test dataset from CSV file."""
    data = []
    with open(filepath, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            data.append({
                'id': int(row['id']),
                'teks': row['teks_keluhan'],
                'kategori_gt': row['kategori_ground_truth'],
                'urgensi_gt': row['urgensi_ground_truth']
            })
    return data

def predict_single(text, categories, threshold):
    """Send a single prediction request to the NLP API."""
    payload = {
        "description": text,
        "categories": categories,
        "threshold": threshold
    }
    start_time = time.time()
    try:
        response = requests.post(NLP_URL, json=payload, timeout=60)
        elapsed = time.time() - start_time
        if response.status_code == 200:
            result = response.json()
            return {
                'predicted_category': result.get('predicted_category', 'ERROR'),
                'confidence_score': result.get('confidence_score', 0),
                'is_confident': result.get('is_confident', False),
                'natural_prediction': result.get('natural_prediction', ''),
                'cleaned_text': result.get('cleaned_text', ''),
                'latency_ms': round(elapsed * 1000, 2),
                'error': None
            }
        else:
            return {
                'predicted_category': 'ERROR',
                'confidence_score': 0,
                'is_confident': False,
                'natural_prediction': '',
                'cleaned_text': '',
                'latency_ms': round(elapsed * 1000, 2),
                'error': f"HTTP {response.status_code}: {response.text}"
            }
    except Exception as e:
        elapsed = time.time() - start_time
        return {
            'predicted_category': 'ERROR',
            'confidence_score': 0,
            'is_confident': False,
            'natural_prediction': '',
            'cleaned_text': '',
            'latency_ms': round(elapsed * 1000, 2),
            'error': str(e)
        }

def calculate_metrics(y_true, y_pred, labels):
    """Calculate confusion matrix, accuracy, precision, recall, F1 per category."""
    # Build confusion matrix
    label_to_idx = {label: i for i, label in enumerate(labels)}
    n = len(labels)
    confusion = [[0]*n for _ in range(n)]
    
    for gt, pred in zip(y_true, y_pred):
        if gt in label_to_idx and pred in label_to_idx:
            confusion[label_to_idx[gt]][label_to_idx[pred]] += 1
    
    # Calculate per-class metrics
    metrics = {}
    total_correct = 0
    total_samples = len(y_true)
    
    for i, label in enumerate(labels):
        tp = confusion[i][i]
        fp = sum(confusion[j][i] for j in range(n)) - tp
        fn = sum(confusion[i][j] for j in range(n)) - tp
        tn = total_samples - tp - fp - fn
        
        precision = tp / (tp + fp) if (tp + fp) > 0 else 0
        recall = tp / (tp + fn) if (tp + fn) > 0 else 0
        f1 = 2 * precision * recall / (precision + recall) if (precision + recall) > 0 else 0
        support = sum(confusion[i])
        
        metrics[label] = {
            'precision': round(precision, 4),
            'recall': round(recall, 4),
            'f1_score': round(f1, 4),
            'support': support,
            'tp': tp, 'fp': fp, 'fn': fn, 'tn': tn
        }
        total_correct += tp
    
    accuracy = total_correct / total_samples if total_samples > 0 else 0
    
    # Macro averages
    valid_labels = [l for l in labels if metrics[l]['support'] > 0]
    macro_precision = sum(metrics[l]['precision'] for l in valid_labels) / len(valid_labels) if valid_labels else 0
    macro_recall = sum(metrics[l]['recall'] for l in valid_labels) / len(valid_labels) if valid_labels else 0
    macro_f1 = sum(metrics[l]['f1_score'] for l in valid_labels) / len(valid_labels) if valid_labels else 0
    
    return {
        'confusion_matrix': confusion,
        'per_class': metrics,
        'accuracy': round(accuracy, 4),
        'macro_precision': round(macro_precision, 4),
        'macro_recall': round(macro_recall, 4),
        'macro_f1': round(macro_f1, 4),
        'total_samples': total_samples,
        'total_correct': total_correct
    }

def run_evaluation():
    """Main evaluation function."""
    print("=" * 70)
    print("EVALUASI AKURASI NLP ENGINE — IT HELPDESK PTPN IV")
    print("=" * 70)
    
    # Check API availability
    print("\n[1/4] Memeriksa koneksi ke NLP API...")
    try:
        r = requests.get("http://127.0.0.1:8000/", timeout=10)
        print(f"  [OK] API aktif: {r.json()}")
    except Exception as e:
        print(f"  [FAIL] API tidak aktif: {e}")
        print("  Pastikan NLP engine berjalan di http://127.0.0.1:8000")
        sys.exit(1)
    
    # Load test data
    print(f"\n[2/4] Memuat data uji dari {DATA_FILE}...")
    test_data = load_test_data(DATA_FILE)
    print(f"  [OK] {len(test_data)} sampel dimuat")
    
    # Count per category
    cat_counts = defaultdict(int)
    for item in test_data:
        cat_counts[item['kategori_gt']] += 1
    print("  Distribusi per kategori:")
    for cat, cnt in sorted(cat_counts.items()):
        print(f"    - {cat}: {cnt} sampel")
    
    # Run predictions
    print(f"\n[3/4] Menjalankan prediksi ({len(test_data)} request)...")
    results = []
    y_true = []
    y_pred = []
    latencies = []
    fallback_count = 0
    errors = []
    
    for i, item in enumerate(test_data):
        pred = predict_single(item['teks'], CATEGORIES_DB, THRESHOLD)
        
        result_entry = {
            'id': item['id'],
            'teks_keluhan': item['teks'],
            'kategori_ground_truth': item['kategori_gt'],
            'urgensi_ground_truth': item['urgensi_gt'],
            'kategori_prediksi': pred['predicted_category'],
            'confidence_score': pred['confidence_score'],
            'is_confident': pred['is_confident'],
            'natural_prediction': pred['natural_prediction'],
            'cleaned_text': pred['cleaned_text'],
            'latency_ms': pred['latency_ms'],
            'benar': pred['predicted_category'] == item['kategori_gt'],
            'error': pred['error']
        }
        results.append(result_entry)
        
        y_true.append(item['kategori_gt'])
        y_pred.append(pred['predicted_category'])
        latencies.append(pred['latency_ms'])
        
        if pred['predicted_category'] == 'Lain-lain':
            fallback_count += 1
        
        if pred['error']:
            errors.append(f"  Sample #{item['id']}: {pred['error']}")
        
        status = "OK" if result_entry['benar'] else "XX"
        print(f"  [{i+1:3d}/{len(test_data)}] {status} ID={item['id']:3d} | GT: {item['kategori_gt'][:20]:20s} | Pred: {pred['predicted_category'][:20]:20s} | Conf: {pred['confidence_score']:6.2f}% | {pred['latency_ms']:.0f}ms")
    
    # Calculate metrics
    print(f"\n[4/4] Menghitung metrik evaluasi...")
    
    # Include "Lain-lain" in labels if any predictions fell to it
    all_labels = CATEGORIES_DB.copy()
    if fallback_count > 0:
        all_labels.append("Lain-lain")
    
    metrics = calculate_metrics(y_true, y_pred, all_labels)
    
    # Latency stats
    lat_stats = {
        'rata_rata_ms': round(sum(latencies) / len(latencies), 2),
        'minimum_ms': round(min(latencies), 2),
        'maksimum_ms': round(max(latencies), 2),
        'median_ms': round(sorted(latencies)[len(latencies)//2], 2),
        'melebihi_30detik': sum(1 for l in latencies if l > 30000),
        'total_request': len(latencies)
    }
    
    # Print summary
    print("\n" + "=" * 70)
    print("RINGKASAN HASIL EVALUASI")
    print("=" * 70)
    
    print(f"\nTotal Sampel        : {metrics['total_samples']}")
    print(f"Prediksi Benar      : {metrics['total_correct']}")
    print(f"Accuracy            : {metrics['accuracy']*100:.2f}%")
    print(f"Macro Precision     : {metrics['macro_precision']*100:.2f}%")
    print(f"Macro Recall        : {metrics['macro_recall']*100:.2f}%")
    print(f"Macro F1-Score      : {metrics['macro_f1']*100:.2f}%")
    print(f"Fallback 'Lain-lain': {fallback_count} sampel")
    
    print(f"\nLatensi:")
    print(f"  Rata-rata : {lat_stats['rata_rata_ms']:.2f} ms")
    print(f"  Minimum   : {lat_stats['minimum_ms']:.2f} ms")
    print(f"  Maksimum  : {lat_stats['maksimum_ms']:.2f} ms")
    print(f"  Median    : {lat_stats['median_ms']:.2f} ms")
    print(f"  > 30 detik: {lat_stats['melebihi_30detik']} request")
    
    print("\nPer Kategori:")
    print(f"{'Kategori':<25s} {'Precision':>10s} {'Recall':>10s} {'F1-Score':>10s} {'Support':>8s}")
    print("-" * 65)
    for label in all_labels:
        m = metrics['per_class'][label]
        print(f"{label:<25s} {m['precision']*100:9.2f}% {m['recall']*100:9.2f}% {m['f1_score']*100:9.2f}% {m['support']:7d}")
    print("-" * 65)
    print(f"{'Macro Average':<25s} {metrics['macro_precision']*100:9.2f}% {metrics['macro_recall']*100:9.2f}% {metrics['macro_f1']*100:9.2f}%")
    
    # Save results
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    
    # Save detailed results as JSON
    output_json = os.path.join(OUTPUT_DIR, "hasil_evaluasi_detail.json")
    with open(output_json, 'w', encoding='utf-8') as f:
        json.dump({
            'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
            'model': 'mDeBERTa-v3-base-mnli-xnli',
            'threshold': THRESHOLD,
            'metrics': metrics,
            'latency_stats': lat_stats,
            'fallback_count': fallback_count,
            'errors': errors,
            'detailed_results': results
        }, f, ensure_ascii=False, indent=2)
    print(f"\n[OK] Hasil detail disimpan ke: {output_json}")
    
    # Save confusion matrix as CSV
    cm_file = os.path.join(OUTPUT_DIR, "confusion_matrix.csv")
    with open(cm_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['Aktual \\ Prediksi'] + all_labels)
        for i, label in enumerate(all_labels):
            writer.writerow([label] + metrics['confusion_matrix'][i])
    print(f"[OK] Confusion matrix disimpan ke: {cm_file}")
    
    # Save per-class metrics as CSV
    metrics_file = os.path.join(OUTPUT_DIR, "metrik_per_kategori.csv")
    with open(metrics_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['Kategori', 'Precision', 'Recall', 'F1-Score', 'Support', 'TP', 'FP', 'FN'])
        for label in all_labels:
            m = metrics['per_class'][label]
            writer.writerow([label, f"{m['precision']:.4f}", f"{m['recall']:.4f}", f"{m['f1_score']:.4f}", m['support'], m['tp'], m['fp'], m['fn']])
        writer.writerow(['Macro Average', f"{metrics['macro_precision']:.4f}", f"{metrics['macro_recall']:.4f}", f"{metrics['macro_f1']:.4f}", metrics['total_samples']])
    print(f"[OK] Metrik per kategori disimpan ke: {metrics_file}")
    
    # Save latency stats
    lat_file = os.path.join(OUTPUT_DIR, "latensi.json")
    with open(lat_file, 'w', encoding='utf-8') as f:
        json.dump({
            'stats': lat_stats,
            'per_request': [{'id': r['id'], 'latency_ms': r['latency_ms']} for r in results]
        }, f, ensure_ascii=False, indent=2)
    print(f"[OK] Data latensi disimpan ke: {lat_file}")
    
    if errors:
        print(f"\n[WARN] {len(errors)} error ditemukan:")
        for err in errors:
            print(err)
    
    print("\n[OK] Evaluasi selesai!")
    return metrics, lat_stats, results

if __name__ == "__main__":
    run_evaluation()
