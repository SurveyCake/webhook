//
//  Decrypt.swift
//
//  Created by Paul on 2017/1/6.
//  Copyright © 2017年 25sprout. All rights reserved.
//

/*
 Required :
 1. Must link binary with library "Security.framework"
 2. bridge #import <CommonCrypto/CommonCrypto.h>
 
 Description :
 This class including download function with Alamofire, you can remove Alamofire closure and replacing base64Text by base64String.
*/

import Foundation

class Decrypt {
    static func aesDecode(base64String:String, keyString:String, ivString:String) {
        let url = ("THE_STRING_URL").addingPercentEncoding(withAllowedCharacters: CharacterSet.urlQueryAllowed)!
        var mutableURLRequest = URLRequest(url: URL(string: url)!)
        mutableURLRequest.httpMethod = "GET"
        mutableURLRequest.cachePolicy = NSURLRequest.CachePolicy.reloadIgnoringLocalAndRemoteCacheData
        
        Alamofire.request(mutableURLRequest as URLRequestConvertible).validate(statusCode: 200..<300).responseString {
            response in switch response.result {
            case .success(let base64Text):
                // should transform base64Text or base64String and keyString also ivString into data type
                let data = NSData(data: Data(base64Encoded: base64Text, options: NSData.Base64DecodingOptions()))
                let keyData = (keyString as NSString).data(using: String.Encoding.utf8.rawValue) as NSData!
                let ivData = (ivString as NSString).data(using: String.Encoding.utf8.rawValue) as NSData!
                
                if let decryptedData = testCrypt(data: data, keyData:keyData!, ivData:ivData!, operation:UInt32(kCCDecrypt)) {
                    // you can get decoded data here and transform it into string type as sample or using library to transform it into json format directly
                    print("\(String(data: decryptedData as Data, encoding: String.Encoding.utf8))")
                    
                }
            case .failure(let error):
                print(error)
            }
        }
    }
    
    static func testCrypt(data:NSData, keyData:NSData, ivData:NSData, operation:CCOperation) -> NSData? {
        let keyBytes = keyData.bytes.assumingMemoryBound(to: UInt8.self)
        let ivBytes = ivData.bytes.assumingMemoryBound(to: UInt8.self)
        
        let dataLength = Int(data.length)
        let dataBytes = data.bytes.assumingMemoryBound(to: UInt8.self)
        
        let cryptData: NSMutableData! = NSMutableData(length: Int(dataLength) + kCCBlockSizeAES128)
        let cryptPointer = cryptData.mutableBytes.assumingMemoryBound(to: UInt8.self)
        let cryptLength  = size_t(cryptData.length)
        
        let keyLength              = size_t(kCCKeySizeAES128)
        let algoritm:  CCAlgorithm = UInt32(kCCAlgorithmAES128)
        let options:   CCOptions   = UInt32(kCCOptionPKCS7Padding)
        var numBytesEncrypted :size_t = 0
        let cryptStatus = CCCrypt(operation,
                                  algoritm,
                                  options,
                                  keyBytes, keyLength,
                                  ivBytes,
                                  dataBytes, dataLength,
                                  cryptPointer, cryptLength,
                                  &numBytesEncrypted)
        
        if UInt32(cryptStatus) == UInt32(kCCSuccess) {
            cryptData.length = Int(numBytesEncrypted)
            
        } else {
            print("Error: \(cryptStatus)")
        }
        
        return cryptData;
    }
}
