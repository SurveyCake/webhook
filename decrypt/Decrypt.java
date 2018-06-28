package com.crazyma;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import java.nio.charset.Charset;
import java.util.Base64;

/**
 * Created by david on 2017/2/6.
 */
public class Decrypt {

    public static String decrypt(byte[] cipherTextBytes, String key, String iv) throws Exception {
        byte[] ivBytes = iv.getBytes(Charset.forName("UTF-8"));

        SecretKeySpec secretKeySpec = new SecretKeySpec(key.getBytes(Charset.forName("UTF-8")), "AES");

        Cipher cipher = Cipher.getInstance("AES/CBC/NoPadding");
        cipher.init(Cipher.DECRYPT_MODE, secretKeySpec, new IvParameterSpec(ivBytes));

        byte[] decryptedBytes = cipher.doFinal(cipherTextBytes);

        String decryptedText = new String(decryptedBytes, "UTF-8");
        System.out.println("decryption：" + decryptedText);

        return decryptedText;
    }

    public static String decrypt(String cipherTextStr, String key, String iv) throws Exception {
        return decrypt(Base64.getDecoder().decode(cipherTextStr), key, iv);
    }
}
