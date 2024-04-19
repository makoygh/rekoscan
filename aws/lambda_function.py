import boto3
from decimal import Decimal
import json
import urllib.request
import urllib.parse
import urllib.error
import os

print('Loading function')

rekognition = boto3.client('rekognition')

s3_client = boto3.client('s3')
# --------------- Helper Functions to call Rekognition APIs ------------------


def detect_faces(bucket, key):
    response = rekognition.detect_faces(Image={"S3Object": {"Bucket": bucket, "Name": key}},  Attributes=['ALL'])
    return response


def index_faces(bucket, key):
    # Note: Collection has to be created upfront. Use CreateCollection API to create a collecion.
    #rekognition.create_collection(CollectionId='BLUEPRINT_COLLECTION')
    response = rekognition.index_faces(Image={"S3Object": {"Bucket": bucket, "Name": key}}, CollectionId="BLUEPRINT_COLLECTION")
    return response
    

# --------------- Main handler ------------------


def lambda_handler(event, context):
    
    print("Received event: " + json.dumps(event, indent=2))
    bucket = event['Records'][0]['s3']['bucket']['name']
    key = urllib.parse.unquote_plus(event['Records'][0]['s3']['object']['key'])
    try:
        # Calls rekognition DetectFaces API to detect faces in S3 object
        response = detect_faces(bucket, key)
        
        # Check if FaceDetails exist and then process them
        if 'FaceDetails' in response:
            # Extract filename from key, remove 'images/' prefix, change extension to .txt
            new_filename = key.replace('images/', '')
            new_filename = new_filename.rsplit('.', 1)[0] + '.txt'
            
            # Prepare the content as a JSON string
            content = json.dumps(response['FaceDetails'], indent=2)
            
            # Upload the JSON data directly to S3 in the 'face-analyses/' directory
            s3_client.put_object(Body=content, Bucket=bucket, Key='face-analyses/' + new_filename)
        
        
        # Print response to console
        print(response)
        return response
    except Exception as e:
        print(e)
        print(f"Error processing object {key} from bucket {bucket}. Make sure your object and bucket exist and your bucket is in the same region as this function.")
        raise e