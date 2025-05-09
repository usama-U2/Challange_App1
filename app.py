from flask import Flask, jsonify, request
from azure.storage.blob import BlobServiceClient, BlobClient, ContainerClient

app = Flask(__name__)

# Azure Blob Storage connection string (make sure to replace it with your actual connection string)
connection_string = "datacom769"
container_name = "datacom769"

# Initialize the BlobServiceClient
blob_service_client = BlobServiceClient.from_connection_string(connection_string)

@app.route('/images', methods=['POST'])
def upload_blob():
    # Get the file from the request
    file = request.files['file']
    
    # Create the blob client
    blob_client = blob_service_client.get_blob_client(container=container_name, blob=file.filename)
    
    # Upload the file to Azure Blob Storage
    blob_client.upload_blob(file)
    
    return jsonify({"message": "File uploaded successfully"}), 200

@app.route('/login/<filename>', methods=['GET'])
def download_blob(filename):
    # Create the blob client
    blob_client = blob_service_client.get_blob_client(container=container_name, blob=filename)
    
    # Download the blob
    blob_data = blob_client.download_blob()
    content = blob_data.readall()
    
    # Return the content as response (or you can write it to a file, depending on your use case)
    return content, 200

@app.route('/list_blobs', methods=['GET'])
def list_blobs():
    # Get the container client
    container_client = blob_service_client.get_container_client(container_name)
    
    # List the blobs in the container
    blobs = container_client.list_blobs()
    
    blob_list = [blob.name for blob in blobs]
    
    return jsonify(blob_list), 200

if __name__ == '__main__':
    app.run(debug=True)
