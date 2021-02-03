/*
 First request
 We can also use JOIN but if we sure that data in booster pack table is static
 */

SELECT
    user_id, transaction_type, transaction_record AS bootster_pack_id, SUM(amount)
FROM
    test_task.transaction
WHERE
        transaction_subject = 'booster_pack'
  AND time_created BETWEEN SUBDATE(CURDATE(), INTERVAL 1 MONTH) AND NOW()
GROUP BY user_id, transaction_type, transaction_record;


/*
 Second request
 If i correctly understand a question
 (end of the work day =/)
 */
SELECT
    user_id,
    u.likes_balance AS likes_balance,
    u.wallet_balance AS wallet_balance,
    SUM(amount),
    transaction_type,
    transaction_subject
FROM
    transaction
        JOIN
    user u ON u.id = user_id
WHERE
        transaction_subject IN ('topup' , 'booster_pack')
GROUP BY user_id , transaction_type , transaction_subject